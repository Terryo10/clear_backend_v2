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
        ]);

        if ($validator->fails()) {
            return  $this->jsonError(401,'Request failed some fields are required',  $validator->getMessageBag(), 'errors');

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
        $validation =Validator::make( $request->all(),[
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
            return  $this->jsonError(401,'Request failed some fields are required',  $validation->getMessageBag(), 'errors');

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
            'attachment' =>$path,
            'contract_terms_conditions' => $data['contract_terms_conditions'],
            'execution_plan' => $data['execution_plan'],
            'site_info' => $data['site_info'],
            'scope_of_work' => $data['scope_of_work'],
        ]);
        //get contractor
        return $this->jsonSuccess(200, 'Proposal Created', 'proposal', 'proposal');
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

}
