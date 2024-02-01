<?php

namespace App\Interfaces\Images;

use Illuminate\Support\Facades\Storage;

class ImageRepo implements ImageRepoInterface
{

    public function uploadImage($image, $path)
    {
        return  $image->store($path, ['disk' => 'public']);
    }

    public function deleteImage($image)
    {
        return Storage::disk('public')->delete($image);
    }

    public function uploadBulkImages($images, $path)
    {
        $imagesArray = [];
        foreach ($images as $image) {
            $imagesArray[] = $image->store($path, ['disk' => 'public']);
        }
        return true;
    }

    //create bulk image upload and save relationship
    public function uploadBulkImagesAndSaveRelationship($images, $path, $model)
    {
        foreach ($images as $image) {
            $path = $this->uploadImage($image, $path);
            $model->images()->create(['image_name' => $path]);
        }
        return true;
    }
    public function uploadBulkFilesAndSaveRelationship($files, $path, $model)
    {
        foreach ($files as $image) {
            $path = $this->uploadImage($image, $path);
            $model->scopeFiles()->create(['file_name' => $path]);
        }
        return true;
    }

    //create function to delete bulk images
    public function deleteBulkImages($images)
    {
        foreach ($images as $image) {
            Storage::disk('public')->delete($image);
        }
        return true;
    }
}
