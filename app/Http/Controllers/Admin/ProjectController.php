<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
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
                'user_id' => 'nullable',
                'state' => 'nullable',
                'project_color' => 'nullable',
        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
