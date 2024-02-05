<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function chat()
    {
        return $this->belongsTo(GroupChat::class);
    }

    //check if message is from current logged in user
    public function isFromCurrentUser()
    {
        return $this->user_id == auth()->user()->id;
    }

    //cast create at to time online
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    //create function to get message attatchment file type

    function getFileType()
    {
        if (!$this->attachement) {
            return null;
        }
        $path_info = pathinfo($this->attachement);

        return $path_info['extension'];
    }
    function getFileName()
    {
        if (!$this->attachement) {
            return null;
        }
        if ($this->isImage()) {
            return 'image';
        }
        if ($this->isVideo()) {
            return 'video';
        }
        if ($this->isAudio()) {
            return 'audio';
        }
        if ($this->isFile()) {
            return 'file';
        }
        return null;
    }

    //check extension of file is image
    public function isImage()
    {
        $fileType = $this->getFileType();
        if ($fileType == 'jpg' || $fileType == 'png' || $fileType == 'jpeg') {
            return true;
        }
        return false;
    }

    //check extension of file is video
    public function isVideo()
    {
        $fileType = $this->getFileType();
        if ($fileType == 'mp4' || $fileType == 'avi' || $fileType == 'mov') {
            return true;
        }
        return false;
    }

    //check extension of file is audio
    public function isAudio()
    {
        $fileType = $this->getFileType();
        if ($fileType == 'mp3' || $fileType == 'wav' || $fileType == 'ogg' || $fileType == 'm4a') {
            return true;
        }
        return false;
    }

    //check extension of file
    public function isFile()
    {
        $fileType = $this->getFileType();
        if ($fileType == 'pdf' || $fileType == 'doc' || $fileType == 'docx' || $fileType == 'xls' || $fileType == 'xlsx' || $fileType == 'ppt' || $fileType == 'pptx' || $fileType == 'txt') {
            return true;
        }
        return false;
    }
}
