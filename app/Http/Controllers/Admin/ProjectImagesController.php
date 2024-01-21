<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectImagesController extends Controller
{
    public function uploadImagesFromAdmin(Request $request)
    {
        $project = Project::find($request->project_id);

        if ($request->hasFile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {
                $path = $image->store('images'); // Store the image in the storage/app/images directory

                $project->images()->create([
                    'image_name' => $path,
                ]);
            }
        }

        if ($request->hasFile('scopeFiles')) {
            $scopeFiles = $request->file('scopeFiles');

            foreach ($scopeFiles as $file) {
                $path = $file->store('scopeFiles'); // Store the file in the storage/app/scopeFiles directory

                $project->scopeFiles()->create([
                    'file_name' => $path,
                ]);
            }
        }
    }


}