<?php

namespace App\Interfaces\Proposals;

use App\Interfaces\Chat\ChatRepoInterface;
use App\Interfaces\Images\ImageRepoInterface;
use App\Interfaces\Notifications\NotificationRepoInterface;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\RequestProposal;
use App\Models\User;
use Dompdf\FrameDecorator\Image;

class RequestProposalRepo implements RequestProposalRepoInterface
{
    public $notificationRepoInterface;
    public $imageRepoInterface;

    public function __construct(NotificationRepoInterface $notificationRepoInterface, ImageRepoInterface $imageRepoInterface, ChatRepoInterface $chatRepoInterface)
    {
        $this->notificationRepoInterface = $notificationRepoInterface;
        $this->imageRepoInterface = $imageRepoInterface;
    }
    public function createRequestProposals($data, $project_id)
    {
        $contractors = array();
        foreach ($data['contractors'] as $contractor) {
            //get contractor
            $contractor = User::find($contractor['id']);
            array_push($contractors, $contractor);
            $this->notificationRepoInterface->sendEmailNotification([
                'email' => $contractor->email,
                'subject' => 'New Request Proposal',
                'email_message' => 'You have a new project request proposal please check your dashboard for more details. And Submit your proposal bid.',
            ]);
            RequestProposal::create([
                'project_id' => $project_id,
                'contractor_id' => $contractor->id,
                'description' => $data['message'],
                'status' => 'sourcing_bid',
            ]);
            //send email to contractor
        }

        $this->notificationRepoInterface->broadCastNotification($contractors, [
            'title' => 'New Request Proposal',
            'body' => 'You have a new project request proposal please check your dashboard for more details. And Submit your proposal bid.',
            'type' => 'Request',
        ]);

        return true;
    }

    public function removeContractor($id)
    {
        $requestProposal = RequestProposal::find($id);
        $requestProposal->delete();

        return true;
    }

    //get all request proposal for a contractor
    public function getRequestProposals($contractor_id)
    {
        return RequestProposal::where('contractor_id', $contractor_id)
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
    }

    public function createProposal(array $data)
    {
        $contractor = auth()->user();

        $project = Project::find($data['project_id']);
        $project->proposals()->create([
            'cost' => $data['cost'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'contractor_id' => $contractor->id,
            'attachment' => isset($data['attachment']) ? $this->imageRepoInterface->uploadImage($data['attachment'], 'projects/files') : null,
            'execution_plan' => $data['execution_plan'],
            'scope_of_work' => $data['scope_of_work'],
        ]);

        $proposalRequest = RequestProposal::where('project_id', $data['project_id'])->where('contractor_id', $contractor->id)->first();
        $proposalRequest->status = 'bid_submitted';
        $proposalRequest->save();
        $project->setProjectStatus('first_bid_received');

        return true;
    }

    public function findProposal($project_id, $contractor_id)
    {
        return Proposal::where('project_id', $project_id)->where('contractor_id', $contractor_id)->first();
    }

    public function updateProposal(array $data)
    {
        $proposal = Proposal::find($data['proposal_id']);
        $proposal->cost = $data['cost'];
        $proposal->description = $data['description'];
        $proposal->start_date = $data['start_date'];
        $proposal->end_date = $data['end_date'];
        $proposal->scope_of_work = $data['scope_of_work'];
        $proposal->execution_plan = $data['execution_plan'];

        //check if user upload new file
        if (isset($data['attachment'])) {
            //delete old file
            $this->imageRepoInterface->deleteImage($proposal->attachment);
            //upload new file
            $proposal->attachment = $this->imageRepoInterface->uploadImage($data['attachment'], 'projects/files');
        }

        $proposal->save();
        return true;
    }

    public function deleteProposal($id, $request_proposal_id)
    {
        $proposal = Proposal::where('id', $id)->first();
        $requestProposal = RequestProposal::where('id', $request_proposal_id)->first();

        $requestProposal->status = 'withdrawn';
        $requestProposal->save();

        $proposal->delete();
    }
}
