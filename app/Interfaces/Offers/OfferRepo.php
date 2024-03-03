<?php

namespace App\Interfaces\Offers;

use App\Events\NotifyUser;
use App\Interfaces\Chat\ChatRepoInterface;
use App\Interfaces\Images\ImageRepoInterface;
use App\Interfaces\Notifications\NotificationRepoInterface;
use App\Interfaces\Offers\OfferRepoInterface;
use App\Interfaces\Project\ProjectRepoInterface;
use App\Models\OfferOption;
use App\Models\OfferOptions;
use App\Models\Project;
use App\Models\ProjectOffer;
use App\Models\ProjectOffers;
use App\Models\Notification as ModelsNotification;

class OfferRepo implements OfferRepoInterface
{
    protected $projectRepo;
    protected $notificationRepo;
    protected $imageRepo;

    public function __construct(ImageRepoInterface $imageRepo, ProjectRepoInterface $projectRepo, NotificationRepoInterface $notificationRepo)
    {
        $this->projectRepo = $projectRepo;
        $this->notificationRepo = $notificationRepo;
        $this->imageRepo = $imageRepo;
    }
    public function create(array $data, $options)
    {
        $project = Project::findOrFail($data['project_id']);
        $project->setProjectStatus('bids_ready_for_approval');

        $data['status'] = 'pending_client_sign';

        $offer = ProjectOffers::create($data);
        $this->createOfferOptions($options, $offer);
        $this->projectRepo->createProjectHistory($project, 'bids_ready_for_approval', 'Bids ready for approval');
        $this->notificationRepo->sendEmailNotification([
            'subject' => 'Offer Created',
            'email_message' => 'Offer for project ' . $project->title . ' has been created and is ready for approval',
            'email' => $project->user->email,
        ]);

        $this->notificationRepo->broadCastNotification([$project->user], [
            'title' => 'Offer Created',
            'body' => 'Offer for project ' . $project->title . ' has been created and is ready for approval',
            'type' => 'Request'
        ]);
        return $offer;
    }

    public function update(array $data, $options)
    {
        $project  = Project::find($data['project_id']);

        $offer = $project->offer;
        $offer->update($data);
        $offer->options()->delete();
        $offer->options()->createMany($options);
        return $offer;
    }

    public function acceptOffer($offerId, $data)
    {
        $offer = ProjectOffers::find($offerId);
        $image = str_replace('data:image/png;base64,', '', $data['manager_signature']);
        $image = str_replace(' ', '+', $image);
        $imageName = time() . '.png';

        $data['manager_signature'] = $this->imageRepo->uploadImage($data['manager_signature'], 'projects/offers/manager_signatures');

        $offer->manager_signature =  $data['manager_signature'];
        $offer->status = "manager_signed";
        $offer->save();

        $offer->project->setProjectStatus('project_in_progress');
        $this->projectRepo->createProjectHistory(
            $offer->project,
            'project_in_progress',
            'Project in progress'
        );
        $offer->project->contractor_id = $offer->selectedOption->contractor_id;
        $offer->project->save();

        $this->notificationRepo->sendEmailNotification([
            'subject' => 'manager_signed',
            'email_message' => 'Offer for project ' . $offer->project->title . ' has been accepted',
            'email' => $offer->project->user->email,
        ]);
        $notification = ModelsNotification::create([
            'title' => 'Offer Accepted',
            'body' => 'Offer for project ' . $offer->project->title . ' has been accepted',
            'type' => 'Request'
        ]);
        broadcast(new NotifyUser($offer->project->user, $notification))->toOthers();
        $this->notificationRepo->broadCastNotification([$offer->project->user], [
            'title' => 'Offer Accepted',
            'body' => 'Offer for project ' . $offer->project->title . ' has been accepted',
            'type' => 'Request'
        ]);
        return $offer;
    }

    public function sign($offerId, $data)
    {
        $offer = ProjectOffers::find($offerId);
        $option = OfferOptions::find($data['selected_option']);

        $data['signature'] = $this->imageRepo->uploadImage($data['signature'], 'projects/offers/signatures');
        $offer->selected_option = $data['selected_option'];

        //upload signed document
        $offer->signature =   $data['signature'];
        $offer->status = "client_signed";
        $offer->contract_terms_conditions = $option->contract_terms_conditions;
        $offer->execution_plan = $option->execution_plan;
        $offer->scope_of_work = $option->scope_of_work;
        $offer->site_info = $option->site_info;
        $offer->save();
        $offer->project->setProjectStatus("project_being_scheduled");

        $this->projectRepo->createProjectHistory($offer->project, 'project_being_scheduled', 'Project being scheduled');


        $this->notificationRepo->sendEmailNotification([
            'subject' => 'Offer Signed',
            'email_message' => 'Offer for project ' . $offer->project->title . ' has been signed',
            'email' => $offer->project->user->email,
        ]);
        $this->notificationRepo->broadCastNotification([$offer->project->user], [
            'title' => 'Offer Signed',
            'body' => 'Offer for project ' . $offer->project->title . ' has been signed',
            'type' => 'Request'
        ]);
        $notification = ModelsNotification::create([
            'title' => 'Offer Signed',
            'body' => 'Option ' . $option->option_name . "with Budget " . $option->cost . ' has been signed by Client',
            'type' => 'Option Signed'
        ]);
        broadcast(new NotifyUser($option->contractor_id, $notification))->toOthers();
        return true;
    }

    public function createOfferOptions(array $options, ProjectOffers $offer)
    {
        $offer->options()->createMany($options);
    }
}
