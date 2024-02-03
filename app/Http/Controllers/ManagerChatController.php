<?php

namespace App\Http\Controllers;

use App\Http\Resources\ManagerChatResource;
use App\Interfaces\Notifications\NotificationRepoInterface;
use App\Models\ManagerChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class     ManagerChatController extends Controller
{
    public $notificationRepoInterface;
    public function __construct(NotificationRepoInterface $notificationRepoInterface)
    {
        $this->notificationRepoInterface = $notificationRepoInterface;
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $managerChatFound = ManagerChat::where('user_id', $user->id)->first(); {
        if ($managerChatFound) {
            return $this->jsonError(400, 'You Already Have Chat Request', null, 'chat_request');
        }
    }

        $managerChat = ManagerChat::create([
            'user_id' => $user->id,
            'accepted' => false,
        ]);

        return $this->jsonSuccess(200, 'Chat Request Send Clear Manager Will contact you soon', new ManagerChatResource($managerChat), 'manager_chat');
    }


    public function storeContractorChatRequest(Request $request)
    {
        $user = Auth::user();

        //check if user has chat request
        $managerChatFound = ManagerChat::where('user_id', $user->id)->first(); {
        if ($managerChatFound) {
            return $this->jsonError(400, 'You Already Have Chat Request', null, 'chat_request');
        }
    }
        $managerChat = ManagerChat::create([
            'user_id' => $user->id,
            'accepted' => false,
        ]);
        $role = 'admin';
        $adminUsers = User::all()->filter(function ($user) use ($role) {
            return $user->roles->first()->role->name == $role;
        });
        $this->broadcastNotification($adminUsers, [
            'title' => 'New Chat Request',
            'body' => 'New Chat Request from ' . $user->first_name . ' ' . $user->last_name,
            'type' => 'chat_request',
        ]);
        return $this->jsonSuccess(200, 'Chat Request Send Clear Manager Will contact you soon', null, 'manager_chat');
    }

    public function show(ManagerChat $managerChat)
    {
        return $this->jsonSuccess(200, 'Manager Chat', new ManagerChatResource($managerChat), 'manager_chat');
    }

    public function index()
    {
        $user = Auth::user();
        $managerChats = $user->managerChats()->get();
        return $this->jsonSuccess(200, 'Manager Chats', ManagerChatResource::collection($managerChats), 'manager_chats');
    }

    //create function for to accepted chat request
    public function acceptChatRequest(ManagerChat $managerChat)
    {
        if ($managerChat->manager_id == Auth::user()->id) {
            return back()->with('message', 'You have aready accepted request');
        }
        $managerChat->update(['accepted' => true, 'manager_id' => Auth::user()->id]);

        $this->notificationRepoInterface->broadCastNotification([$managerChat->user], [
            'title' => 'Chat Request Accepted',
            'body' => 'Your Chat Request has been accepted',
            'type' => 'chat_request',
        ]);

        return $this->jsonSuccess(200, 'Chat Request Accepted', new ManagerChatResource($managerChat), 'manager_chat');
    }
}
