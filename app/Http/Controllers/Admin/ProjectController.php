<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\RequestProposal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::paginate(10);
        return $this->jsonSuccess(200, 'Request Successful', $projects, 'projects');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
                'user_id' => 'required|numeric'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 401, 'message' => "validation failed", "errors" => $validator->errors()]);
        } else {
            $inputs = [
                'title' => $request->title,
                'description' => $request->description,
                'service_id' => $request->service_id,
                'street' => $request->street,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'lat' => '',
                'lng' => '',
                'budget' => $request->budget,
                'frequency' => $request->frequency,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'key_factor' => $request->key_factor,
                'additionalRequirements' => '',
                'state' => '',
                'user_id' => $request->user_id
            ];
            try {
                $project =  Project::create($inputs);
                return $this->jsonSuccess(200, 'Request Successful', $project, 'project');
            } catch (\Error $exception) {
                return response()->json(['status' => 402, 'message' => "validation failed", $exception]);
            }
        }
    }


    public function sendContrators(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required',
            'contractors' => 'required',
            'message' => 'required'
        ]);
        $project = Project::find($data['project_id']);
        $project->setProjectStatus('sourcing_of_vendors');
        //loop trough contractors
        $users = [$project->user];
        foreach ($data['contractors'] as $contractor) {
            //get contractor
            $contractor = User::find($contractor['id']);
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

    public function removeContratorRequestForProposal(Request $request)
    {
        $requestProposal = RequestProposal::find($request->id);
        $requestProposal->delete();
        return $this->jsonSuccess(200, 'Proposal Requests Contractor Removed', 'proposal', 'proposal');
    }

    public function sendPropsal(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required',
            'cost' => 'required',
            'description' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'required',
            'contract_terms_conditions' => 'required',
            'execution_plan' => 'required',
            'site_info' => 'required',
            'scope_of_work' => 'required'
        ]);

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
        if ($request->file('attachment')) {
        }
        $project->proposals()->create([
            'cost' => $data['cost'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'contractor_id' => $contractor->id,
            'attachment' => $request->hasFile('attachment') ? $this->imageRepoInterface->uploadImage($request->file('attachment'), 'projects/files') : null,
            'contract_terms_conditions' => $data['contract_terms_conditions'],
            'execution_plan' => $data['execution_plan'],
            'site_info' => $data['site_info'],
            'scope_of_work' => $data['scope_of_work'],
        ]);
        //get contractor
        return $this->jsonSuccess(200, 'Proposal Requests Contractor Removed', 'proposal', 'proposal');
    }

    public function search(Request $request)
    {
        $search = $request->get('query');
        $requests = Project::where('title', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->orWhere('status', 'like', '%' . $search . '%')
            ->orWhereHas('service', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('user', function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');
            })
            ->paginate(20);
        return ProjectResource::collection($requests);
    }

    public function clientProjects()
    {
        $user = auth()->user();

        // get projects for user with staus Project In Progress or Completed


        $projects = Project::where('user_id', auth()->user()->id)->where(function ($query) {
            $query->where('status', "Project In Progress")
                ->orWhere('status', "Completed");
        })->orderBy('created_at', 'DESC')
            ->paginate(20);

        //        $managerChats = ManagerChat::where('accepted', true)->where('user_id', auth()->user()->id)->get();

        return $this->jsonSuccess(200, 'Request Successful', $projects, 'projects');
    }
}
