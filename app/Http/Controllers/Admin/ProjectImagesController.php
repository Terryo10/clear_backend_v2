<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImages;
use App\Models\ProjectScopeFiles;
use Illuminate\Http\Request;

class ProjectImagesController extends Controller
{
    public function deleteProjectFile(Request $request, $id)
    {
        $fileType = $request->type;
        if ($fileType === "scope") {
            $deleteFile = ProjectScopeFiles::where('id', $id)->delete();
        } else {
            $deleteFile = ProjectImages::where('id', $id)->delete();
        }
        return response()->json(['status' => 200, 'message' => 'File Deleted Successfully']);
    }
    public function uploadImagesFromAdmin(Request $request)
    {
        $project = Project::findOrfail($request->project_id);

        if ($request->hasFile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {
                $path = $image->store('images'); // Store the image in the storage/app/images directory

                $project->images()->create([
                    'project_id' => $request->project_id,
                    'path' => $path,
                ]);
            }
        }

        if ($request->hasFile('scopeFiles')) {
            $scopeFiles = $request->file('scopeFiles');

            foreach ($scopeFiles as $file) {
                $path = $file->store('scopeFiles'); // Store the file in the storage/app/scopeFiles directory

                $project->scopeFiles()->create([
                    'project_id' => $request->project_id,
                    'path' => $path,
                ]);
            }
        }
        return $this->jsonSuccess(200, 'Files Uploaded Successfully', $project, 'projects');
    }
}
