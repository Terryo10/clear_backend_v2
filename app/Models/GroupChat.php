<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    use HasFactory;
    protected $guarded;
    protected $with = ['users'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function users()
    {
        return $this->hasMany(ChatUser::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'ASC');
    }

    //create function to current logged in user is in the chat
    public function isUserInChat()
    {
        return ChatUser::where('user_id', auth()->user()->id)->where('group_chat_id', $this->id)->exists();
    }

    //get main user of the chat
    public function getMainUser()
    {
        return $this->users()->where('user_id', $this->project->user_id)->first();
    }

    //get last message of the chat
    public function getLastMessage()
    {
        return $this->hasMany(Message::class)->latest()->first();
    }

    //get the fist message of the chat
    public function getFirstMessage()
    {
        return $this->messages()->first();
    }

    //check if logged in user is in chat
    public function isUserInChatWithId()
    {
        return ChatUser::where('user_id', auth()->user()->id)->where('chat_id', $this->id)->exists();
    }

}
