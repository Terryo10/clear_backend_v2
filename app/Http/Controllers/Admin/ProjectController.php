<?php

namespace App\Http\Controllers\Admin;

use App\Events\NotifyUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Interfaces\Chat\ChatRepoInterface;
use App\Interfaces\Images\ImageRepoInterface;
use App\Interfaces\Notifications\NotificationRepoInterface;
use App\Interfaces\Project\ProjectRepoInterface;
use App\Interfaces\Proposals\RequestProposalRepoInterface;
use App\Models\ManagerChat;
use App\Models\Project;
use App\Models\ProjectRating;
use App\Models\Proposal;
use App\Models\RequestProposal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification as ModelsNotification;

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


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::where(function ($query) {
            $query->where('status', "project_in_progress")
                ->orWhere('status', "project_completed");
        })->latest()->paginate(20);
        return $this->jsonSuccess(200, 'Request Successful', $projects, 'projects');
    }

    public function show($id)
    {
        $project = Project::find($id);
        return $this->jsonSuccess(200, 'Request Successful', $project, 'project');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'service_id' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'budget' => 'required|numeric',
            'frequency' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'key_factor' => 'required',
            'additionalRequirements' => 'nullable',
            'state' => 'nullable',
            'user_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->jsonError(401, 'Request failed. Some fields are required', $validator->errors(), 'errors');
        }

        $inputs = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'service_id' => $request->input('service_id'),
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'zip_code' => $request->input('zip_code'),
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
            'budget' => $request->input('budget'),
            'frequency' => $request->input('frequency'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'key_factor' => $request->input('key_factor'),
            'additionalRequirements' => $request->input('additionalRequirements', ''),
            'state' => $request->input('state', ''),
            'user_id' => $request->input('user_id')
        ];

        try {
            $project = Project::create($inputs);
            $project->setProjectStatus('request_for_bids_received');
            $project->history()->create([
                "status" => $project->status,
                "user_id" => $request->input('user_id'),
            ]);

            $chat = $this->chatRepoInterface->createChat([
                'name' => $project->title,
                'project_id' => $project->id,
            ]);
            $this->chatRepoInterface->addUsersToChat($chat, [$project->user]);

            return $this->jsonSuccess(200, 'Request Successful', $project, 'project');
        } catch (\Exception $exception) {
            return response()->json(['status' => 402, 'message' => 'Validation failed', 'error' => $exception->getMessage()]);
        }
    }

    public function updateProject(Request $request)
    {
        //update project
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'service_id' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'budget' => 'required|numeric',
            'frequency' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'key_factor' => 'required',
            'additionalRequirements' => 'nullable',
            'state' => 'nullable',
            'user_id' => 'required|numeric',
            'project_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->jsonError(401, 'Request failed. Some fields are required', $validator->errors(), 'errors');
        }

        $inputs = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'service_id' => $request->input('service_id'),
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'zip_code' => $request->input('zip_code'),
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
            'budget' => $request->input('budget'),
            'frequency' => $request->input('frequency'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'key_factor' => $request->input('key_factor'),
            'additionalRequirements' => $request->input('additionalRequirements', ''),
            'state' => $request->input('state', ''),
            'user_id' => $request->input('user_id')
        ];

        try {
            $project = Project::find($request->input('project_id'));
            $project->update($inputs);
            $project->history()->create([
                "status" => $project->status,
                "user_id" => $request->input('user_id'),
            ]);


            return $this->jsonSuccess(200, 'Request Successful', $project, 'project');
        } catch (\Exception $exception) {
            return response()->json(['status' => 402, 'message' => 'Validation failed', 'error' => $exception->getMessage()]);
        }
    }


    public function sendProjectToContractors(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'project_id' => 'required',
                'contractors' => 'required',
                'message' => 'required'
            ]
        );

        if ($validator->fails()) {
            return  $this->jsonError(401, 'Request failed some fields are required',  $validator->getMessageBag(), 'errors');
        }

        $data = $request->all();


        $project = Project::find($data['project_id']);
        $project->setProjectStatus('sourcing_of_vendors');
        $users = [$project->user];
        foreach ($data['contractors'] as $contractor_id) {
            //get contractor
            $contractor = User::find($contractor_id);
            array_push($users, $contractor);
            $this->sendEmail("Request Proposal", "You have new incoming project request please review on dashboard and submit proposal", $contractor->email);
            RequestProposal::create([
                'project_id' => $project->id,
                'contractor_id' => $contractor->id,
                'description' => $data['message'],
                'status' => $project->getProjectStatus(),
            ]);
            //send email to contractor
        }
        $notification =
            [
                'title' => 'New Project Request',
                'body' => 'New Incoming Project Request please review',
                'type' => 'Request',
            ];
        // $notification->save();
        $this->broadcastNotification($users, $notification);
        return $this->jsonSuccess(200, 'Proposal Requests Sent', 'proposal', 'proposal');
    }

    public function removeContractorRequestForProposal(Request $request)
    {
        $requestProposal = RequestProposal::find($request->id);
        $requestProposal->delete();
        return $this->jsonSuccess(200, 'Proposal Requests Contractor Removed', 'proposal', 'proposal');
    }

    public function sendProposal(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'project_id' => 'required',
            'cost' => 'required | numeric',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'contract_terms_conditions' => 'required',
            'execution_plan' => 'required',
            'site_info' => 'required',
            'scope_of_work' => 'required'
        ]);

        if ($validation->fails()) {
            return  $this->jsonError(401, 'Request failed some fields are required',  $validation->getMessageBag(), 'errors');
        }

        $data = $request->all();


        if (!$request->has('attachment') && ($request->description == null || $request->description == " ")) {
            return $this->jsonError(
                400,
                'Please add a file or detailed description',
                'proposal',
                'proposal'
            );
        }


        $proposal = Proposal::where('project_id', $data['project_id'])->where('contractor_id', auth()->user()->id)->first();
        if ($proposal) {
            return $this->jsonError(
                400,
                'You have already sent a proposal for this project',
                'proposal',
                'proposal'
            );
        }

        $project = Project::find($data['project_id']);
        $project->checkStatus();

        $proposalRequest = RequestProposal::where('project_id', $data['project_id'])->where('contractor_id', auth()->user()->id)->first();
        $proposalRequest->status = 'Proposal Sent';
        $proposalRequest->save();

        $contractor = User::find(Auth::user()->id);
        $path = '';
        if ($request->file('attachment')) {
            $path = $request->file('attachment')->store('projects/files');
        }
        $project->proposals()->create([
            'cost' => $data['cost'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'contractor_id' => $contractor->id,
            'attachment' => $path,
            'contract_terms_conditions' => $data['contract_terms_conditions'],
            'execution_plan' => $data['execution_plan'],
            'site_info' => $data['site_info'],
            'scope_of_work' => $data['scope_of_work'],
        ]);
        $notification = ModelsNotification::create([
            'title' => 'Contractor Added Proposal To:',
            'body' => 'Project  ' . $project->title . ' ',
            'type' => 'Global',
            'user_id' => $project->user_id
        ]);
        $this->broadcastNotification(
            [Auth::user()->id],
            $notification
        );
        broadcast(new NotifyUser($project->user_id, $notification))->toOthers();
        //get contractor
        return $this->jsonSuccess(200, 'Proposal Created', 'proposal', 'proposal');
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $search = $request->get('query');
        $status = $request->get('status');
        $service = $request->get('service');
        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');

        $query = Project::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('status', 'like', '%' . $status . '%')->orWhereHas('service', function ($q) use ($service) {
                $q->where('name', 'like', '%' . $service . '%');
            });
        }

        if ($service) {
            $query->whereHas('service', function ($q) use ($service) {
                $q->where('name', 'like', '%' . $service . '%');
            });
        }

        if ($firstName || $lastName) {
            $query->whereHas('user', function ($q) use ($firstName, $lastName) {
                $q->where(function ($q) use ($firstName, $lastName) {
                    if ($firstName) {
                        $q->where('first_name', 'like', '%' . $firstName . '%');
                    }
                    if ($lastName) {
                        $q->orWhere('last_name', 'like', '%' . $lastName . '%');
                    }
                });
            });
        }

        $requests = $query->paginate(20);

        return response()->json(['success' => true, 'message' => 'Request Successful', 'data' => $requests], 200);
    }

    public function getRequests()
    {
        $requests = Project::orderBy('created_at', 'desc')->paginate(20);
        return $this->jsonSuccess(200, 'Request Successful', $requests, 'requests');
        return ProjectResource::collection($requests);
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required',
            'status' => 'required',
        ]);

        $project = Project::find($data['project_id']);
        $project->status = $data['status'];
        $project->save();

        $this->sendEmail('Project Updated', "Project with title " . $project->title . " status changed to " . $data['status'], $project->user->email);
        return $this->jsonSuccess(200, 'Project Status Updated', $project, 'project');
    }

    public function changePaymentStatus(Request $request)
    {
        $data = $request->validate([
            'status' => 'required',
            'project_id' => 'required'
        ]);

        $this->projectRepoInterface->changePaymentStatus($data);

        return $this->jsonSuccess(200, 'Payment Status Updated', $data, 'payment');
    }
    public function setPaymentLink(Request $request)
    {
        $data = $request->validate([
            'status_url' => 'required',
            'url' => 'required',
            'project_id' => 'required'
        ]);

        $this->projectRepoInterface->setPaymentLink($data);

        return $this->jsonSuccess(200, 'Payment Status Updated', $data, 'payment');
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->all();
        $project->update($data);
        return $this->jsonSuccess(200, "Project Updated", new ProjectResource($project), "project");
    }

    public function rate(Request $request)
    {
        $data = $request->validate([
            'star_rating' => 'numeric | required',
            'comment' => 'required',
            'project_id' => 'required'
        ]);

        $project = Project::where('id', $data['project_id'])->get();

        $rating = ProjectRating::create([
            'star_rating' => $data['star_rating'],
            'comment' => $data['comment'],
            'project_id' => $data['project_id'],
        ]);

        return $this->jsonSuccess(200, "Feedback successfully added", $rating, 'feedback');
    }

    public function contactorRequest($id)
    {

        //get auth user
        $user = Auth::user();

        $project = Project::find($id);

        $request = RequestProposal::where('project_id', $project->id)->where('contractor_id', $user->id)->first();

        //get proposal for existing request
        $proposal = Proposal::where('contractor_id', $user->id)->where('project_id', $project->id)->first();

        $managerChats = ManagerChat::where('accepted', true)->where('user_id', $user->id)->get();
        return $this->jsonSuccess(
            200,
            "Request found",
            [
                'request' => $request,
                'proposal' => $proposal,
                'managerChats' => $managerChats
            ],
            'request'
        );
    }
}
