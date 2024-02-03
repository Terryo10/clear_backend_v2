<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
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
                $this->broadcastNotification(
                    [Auth::user()->id], $notification);
                return $this->jsonSuccess(200, 'Request Successful', $project , 'project');
            }catch (\Exception $exception) {
                return response()->json(['status' => 401, 'message' => "Error", $exception]);
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
