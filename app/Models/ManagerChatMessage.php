<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerChatMessage extends Model
{
    use HasFactory;
    protected $guarded;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function chat()
    {
        return $this->belongsTo(ManagerChat::class, 'manager_chat_id', 'id');
    }

    //check if message is from current logged in user
    public function isFromCurrentUser()
    {
        return $this->user_id == auth()->user()->id;
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function serviceReqest()
    {
        $this->hasOne(ContractorRequest::class,'message_id' , 'id');
    }

    function getFileType()
    {
        if (!$this->attachment) {
            return null;
        }
        $path_info = pathinfo($this->attachment);

        return $path_info['extension'];
    }

    function getFileName(){
        if (!$this->attachment) {
            return null;
        }
        if($this->isImage()){
            return 'image';
        }
        if($this->isVideo()){
            return 'video';
        }
        if($this->isAudio()){
            return 'audio';
        }
        if($this->isFile()){
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
        if ($fileType == 'mp3' || $fileType == 'wav' || $fileType == 'ogg') {
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
