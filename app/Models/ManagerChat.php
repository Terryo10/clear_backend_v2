<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ManagerChat extends Model
{
    use HasFactory;
    protected $guarded;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(ManagerChatMessage::class);
    }

    //get last message of the chat
    public function getLastMessage()
    {
        return $this->hasMany(ManagerChatMessage::class)->latest()->first();
    }

    //get the fist message of the chat
    public function getFirstMessage()
    {
        return $this->messages()->first();
    }

    public function isUserInChat()
    {
        $user = Auth::user();
        return $user->hasRole('admin') ?
            ManagerChat::where('manager_id', $user->id)->exists()
            : ManagerChat::where('user_id', $user->id)->exists();
    }
    //date cast diff for humans
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
}
