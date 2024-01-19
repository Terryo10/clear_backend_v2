<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectImagesController extends Controller
{
    public function uploadImagesFromAdmin(Request $request){

        if ($request->file('images')) {
            //loop through images
            foreach ($request->file('images') as $image) {
                $path = $this->imageRepoInterface->uploadImage($image, 'projects/images');
//                $project->images()->create([
//                    "image_name" => $path,
//                ]);
            }
            // $this->imageRepoInterface->uploadBulkImagesAndSaveRelationship($request->file('images'), 'projects', $project);
        }
        if ($request->hasFile('scopeFiles')) {
            $path = $this->imageRepoInterface->uploadImage($request->file('scopeFiles'), 'projects/file');
            $project->scopeFiles()->create([
                "file_name" => $path,
            ]);
            // $this->imageRepoInterface->uploadBulkFilesAndSaveRelationship($request->file('scopeFiles'), 'projects', $project);
        }
    }


}
