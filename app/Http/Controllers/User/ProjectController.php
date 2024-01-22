<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
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
                return $this->jsonSuccess(200, 'Request Successful', $project , 'project');
            }catch (\Error $exception) {
                return response()->json(['status' => 401, 'message' => "validation failed", $exception]);
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

        //        $managerChats = ManagerChat::where('accepted', true)->where('user_id', auth()->user()->id)->get();

        return $this->jsonSuccess(200, 'Request Successful', $projects, 'projects');
    }



}
