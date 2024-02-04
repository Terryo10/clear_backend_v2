<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Interfaces\Chat\ChatRepoInterface;
use App\Interfaces\Images\ImageRepoInterface;
use App\Interfaces\Notifications\NotificationRepoInterface;
use App\Interfaces\Project\ProjectRepoInterface;
use App\Interfaces\Proposals\RequestProposalRepoInterface;
use App\Models\Project;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public $projectRepoInterface;
    public $imageRepoInterface;
    public $chatRepoInterface;
    public $notificationRepoInterface;
    public $requestProposalRepoInterface;

    public function __construct(
        NotificationRepoInterface $notificationRepoInterface,
        ProjectRepoInterface $projectRepoInterface,
        ImageRepoInterface $imageRepoInterface,
        ChatRepoInterface $chatRepoInterface,
        RequestProposalRepoInterface $requestProposalRepoInterface
    ) {
        $this->projectRepoInterface = $projectRepoInterface;
        $this->imageRepoInterface = $imageRepoInterface;
        $this->chatRepoInterface = $chatRepoInterface;
        $this->notificationRepoInterface = $notificationRepoInterface;
        $this->requestProposalRepoInterface = $requestProposalRepoInterface;
    }
    public function createProject(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'service_id' => 'required',
                'street' => 'required',
                'city' => 'required',
                'zip_code' => 'required',
                'lat' => 'nullable',
                'lng' => 'nullable',
                'budget' => 'required | numeric',
                'frequency' => 'required',
                'start_date' => 'required ',
                'end_date' => 'required ',
                'key_factor' => 'required',
                'additionalRequirements' => 'nullable',
                'state' => 'nullable',
            ]
        );

        if($validator->fails()){
            return response()->json(['status' => 401, 'message' => "validation failed", "errors" => $validator->errors()]);

        }else{
            try{
                $project =  Project::create(array_merge($request->all(),[ 'user_id' => Auth::user()->id]));
                $notification =
                    [
                        'title' => 'New Project by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name,
                        'body' => 'New Incoming Project Request please review',
                        'type' => 'Global',
                    ];
                $this->notificationRepoInterface->sendEmailNotification(
                    [
                        'subject' => 'Project Request Sent',
                        'email_message' => env('NEW_REQUEST_STRING'),
                        'email' => $project->user->email,
                    ]
                );
                $this->broadcastNotification(
                    [Auth::user()->id], $notification);
                $this->projectRepoInterface->createProjectHistory($project, $project->status, 'Project Created');

                $chat = $this->chatRepoInterface->createChat([
                    'name' => $project->title,
                    'project_id' => $project->id,
                ]);

                $this->chatRepoInterface->addUsersToChat($chat, [$project->user]);
                return $this->jsonSuccess(200, 'Request Successful', $project , 'project');
            }catch (Exception $exception) {
                return response()->json(['status' => 401, 'message' => "Error", "errors" => $exception->getMessage()]);
            }
        }
    }


    public function clientProjects()
    {

        $projects = Project::where('user_id', auth()->user()->id)->where(function ($query) {
            $query->where('status', "Project In Progress")
                ->orWhere('status', "Completed");
        })->orderBy('created_at', 'DESC')
            ->paginate(20);

        return $this->jsonSuccess(200, 'Request Successful', $projects, 'projects');
    }

    public function clientRequests(){
        $projects = Project::where('user_id', auth()->user()->id)->orderBy('created_at', 'DESC')
            ->paginate(20);
        return $this->jsonSuccess(200, 'Request Successful', $projects, 'projects');
    }



}
