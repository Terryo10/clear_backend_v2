<?php

namespace App\Interfaces\Images;

interface ImageRepoInterface
{
    public function uploadImage($image, $path);
    public function deleteImage($image);
    public function uploadBulkImages($images, $path);
    public function uploadBulkImagesAndSaveRelationship($images, $path, $model);
    public function uploadBulkFilesAndSaveRelationship($images, $path, $model);
    public function deleteBulkImages($images);
}
