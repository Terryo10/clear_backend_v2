<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRating;
use Illuminate\Http\Request;

class ProjectRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
    public function storeClient(Request $request)
    {
        $data = $request->validate([
            'star_rating' => ' required',
            'comment' => 'required',
            'project_id' => 'required'
        ]);

        $project = Project::where('id', $data['project_id'])->get();

        $rating = ProjectRating::create([
            'star_rating' => $data['star_rating'],
            'comment' => $data['comment'],
            'project_id' => $data['project_id'],
        ]);

        return redirect()->back()->with('success', 'Feedback successfully added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProjectRating  $projectRating
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectRating $projectRating)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProjectRating  $projectRating
     * @return \Illuminate\Http\Response
     */
    public function edit(ProjectRating $projectRating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProjectRating  $projectRating
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProjectRating $projectRating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProjectRating  $projectRating
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectRating $projectRating)
    {
        //
    }
}
