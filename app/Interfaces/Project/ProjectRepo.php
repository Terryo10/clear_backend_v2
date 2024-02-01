<?php

namespace App\Interfaces\Project;

use App\Interfaces\Chat\ChatRepoInterface;
use App\Interfaces\Images\ImageRepoInterface;
use App\Models\Project;
use App\Models\Transaction;

class ProjectRepo implements ProjectRepoInterface
{
    public $imageRepoInterface;
    public $chatRepoInterface;




    public function __construct(ChatRepoInterface $chatRepoInterface, ImageRepoInterface $imageRepoInterface)
    {
        $this->imageRepoInterface = $imageRepoInterface;
        $this->chatRepoInterface = $chatRepoInterface;
    }

    public function getProject($id)
    {
        return Project::find($id);
    }

    public function getProjects()
    {
        return Project::all();
    }

    //create function to return paginated projects
    public function getPaginatedProjects($perPage)
    {
        return Project::paginate($perPage);
    }

    public function createProject($data)
    {
        $project =  Project::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'service_id' => $data['service_id'],
            'street' => $data['street'],
            'city' => $data['city'],
            'zip_code' => $data['zip_code'],
            // 'lat' => $data['lat'],
            // 'lng' => $data['lng'],
            'budget' => $data['budget'],
            'frequency' => $data['frequency'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'key_factor' => $data['key_factor'],
            'additionalRequirements' => $data['additionalRequirements'],
            'user_id' => $data['user_id'],
            'state' => $data['state'],
            'color' =>isset($data['project_color']) ? $data['project_color'] : "#000000",
        ]);

        $project->setProjectStatus('request_for_bids_received');

        //check if images are present
        if (isset($data['images'])) {
            $this->addImagesToProject($project, $data['images']);
        }

        //check if files are present
        if (isset($data['scopeFiles'])) {
            $this->addFilesToProject($project, $data['scopeFiles']);
        }

        //create project history
        $this->createProjectHistory($project, $project->status, 'Project Created');

        $chat = $this->chatRepoInterface->createChat([
            'name' => $project->title,
            'project_id' => $project->id,
        ]);

        $this->chatRepoInterface->addUsersToChat($chat, [$project->user]);

        return $project;
    }

    public function updateProject($data, $id)
    {
        $project = $this->getProject($id);
        $project->update($data);
        return $project;
    }

    public function deleteProject($id)
    {
        $project = $this->getProject($id);
        $project->delete();
        return $project;
    }


    //create function add images to project
    public function addImagesToProject($project, $images)
    {
        foreach ($images as $key => $image) {
            $path = $this->imageRepoInterface->uploadImage($image, 'projects/images');
            $project->images()->create([
                "image_name" => $path,
                'name' => 'Image' . $key
            ]);
        }
    }

    public function addFilesToProject($project, $file)
    {

        $path = $this->imageRepoInterface->uploadImage($file, 'projects/files');
        $project->scopeFiles()->create([
            "file_name" => $path,
            'name' => 'Scope File'
        ]);
    }

    //function to create project history
    public function createProjectHistory($project, $status, $description)
    {
        $project->history()->create([
            'status' => $status,
            'description' => $description,
            'user_id' => auth()->user()->id,
        ]);

        return true;
    }

    //function to filter projects
    public function filter($title, $status, $user, $from, $to, $service, $contractor)
    {
        $query = Project::query();
        if ($title) {
            $query->where('title', 'LIKE', "%{$title}%");
        }
        if ($status) {
            $query->whereIn('status', $status);
        }

        if ($user) {
            $query->where('user_id', $user);
        }
        if ($contractor) {
            $query->where('contractor_id', $contractor);
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($service) {
            $query->where('service_id', $service);
        }

        return $query->paginate(10);
    }

    public function setPaymentLink(array $data)
    {

        $transaction = Transaction::create([
            'status_url' => $data['url'],
            'project_id' => $data['project_id'],
            'status' => 'pending'
        ]);

        return $transaction;
    }
    public function changePaymentStatus(array $data)
    {

        $transaction = Transaction::where('project_id', $data['project_id'])->first();

        $transaction->update([
            'status' => $data['status']
        ]);

        return $transaction;
    }
}
