<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Models\ChatUser;
use App\Models\GroupChat;
use App\Models\ManagerChat;
use App\Models\ManagerChatMessage;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function adminIndex(Request $request)
    {

        $chats = GroupChat::orderBy('updated_at', 'DESC')->get();
        $chatRequests = ManagerChat::where('accepted', false)->get();
        $managerChats = ManagerChat::where('accepted', true)->where('manager_id', auth()->user()->id)->get();

        return $this->jsonSuccess(200, 'Chat Request Send Clear Manager Will contact you soon', [
            'chats' => ChatResource::collection($chats),
            'chatRequests' => ManagerChatResource::collection($chatRequests),
            'managerChats' => ManagerChatResource::collection($managerChats),
        ], 'manager_chat');
    }
    public function searchChats(Request $request)
    {
        $userSearch = $request->search;


        $chats = Chat::where('name', 'like', '%' . $request->search . '%')->get();

        //search manager chat byt user first name and last name
        $managerChats = ManagerChat::whereHas('user', function ($query) use ($userSearch) {
            $query->where('first_name', 'like', '%' . $userSearch . '%')
                ->orWhere('last_name', 'like', '%' . $userSearch . '%');
        })->orWhereHas('manager', function ($query) use ($userSearch) {
            $query->where('first_name', 'like', '%' . $userSearch . '%')
                ->orWhere('last_name', 'like', '%' . $userSearch . '%');
        })->get();


        return  [
            'chats' => ChatResource::collection($chats),
            'managerChats' => ManagerChatResource::collection($managerChats),
        ];
    }

    public function contractorIndex(Request $request)
    {
        $user = Auth::user();
        $chats = Chat::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        $chatRequests = ManagerChat::where('accepted', false)->get();
        $managerChats = ManagerChat::where('user_id', $user->id)->get();


        return Inertia::render('Contractor/Chat', [
            'chats' => ChatResource::collection($chats),
            'users' => UserResource::collection(User::all()),
            'chatRequests' => ManagerChatResource::collection($chatRequests),
            'managerChats' => ManagerChatResource::collection($managerChats),
        ]);
    }
    public function clientIndex(Request $request)
    {
        $user = Auth::user();
        $chats = Chat::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        $chatRequests = ManagerChat::where('accepted', false)->get();
        $managerChats = ManagerChat::where('user_id', $user->id)->get();

        $projectGroupByMonth = Project::selectRaw('sum(budget) as total, MONTH(created_at) as month')->groupBy('month')->get();
        $lastRequests = Project::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();

        return Inertia::render('Client/Chat', [
            'chats' => ChatResource::collection($chats),
            'users' => UserResource::collection(User::all()),
            'chatRequests' => ManagerChatResource::collection($chatRequests),
            'managerChats' => ManagerChatResource::collection($managerChats),
            "lastRequests" => ProjectResource::collection($lastRequests),
            "projectGroupByMonth" => $projectGroupByMonth,
        ]);
    }
    public function refresh(Request $request)
    {
        return back()->with('message', 'New Message from ' . $request->first_name . ' ' . $request->last_name);
    }

    public function store(Request $request, Project $project)
    {
        $chat = Chat::create([
            'name' => $project->title,
            'project_id' => $project->id,
        ]);
        $chat->users()->create([
            'user_id' => $project->user_id,
        ]);

        return $this->jsonSuccess(200, "Chat Created", new ChatResource($chat), "chat");
    }

    // create function to add users to a chat
    public function addUsers(Request $request, Chat $chat)
    {
        //check if user is in the chat
        $chatUser = ChatUser::where('user_id', $request->user_id)->where('chat_id', $chat->id)->first();
        if ($chatUser) {
            return back()->with('message', 'User already in chat');
        }
        //check if request has users
        $chat->users()->create([
            'user_id' => $request->user_id,
        ]);
        return back()->with('message', 'User added to chat successfully');
    }

    // create function to remove users from a chat
    public function removeUsers(Request $request, Chat $chat)
    {
        //check if request has users
        ChatUser::where('user_id', $request->user_id)->where('chat_id', $chat->id)->delete();
        return back()->with('message', 'User removed from chat successfully');
    }

    // create function to delete a chat
    public function delete(Chat $chat)
    {
        $chat->delete();
        return $this->jsonSuccess(200, "Chat Deleted", null, "chat");
    }

    // create function to get all chats
    public function index()
    {
        $user = Auth::user();
        $chats = Chat::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();


        $managerChats = ManagerChat::where('user_id', auth()->user()->id)->get();

        return $this->jsonSuccess(200, "User chats", ["group_chats" => ChatResource::collection($chats), "manager_chats" => ManagerChatResource::collection($managerChats)], "chats");
    }

    // create function to get a single chat
    public function show(Chat $chat)
    {
        return $this->jsonSuccess(200, "Chat Retrieved", new ChatResource($chat), "chat");
    }

    // create function to update a chat
    public function update(Request $request, Chat $chat)
    {
        $chat->update($request->all());
        return $this->jsonSuccess(200, "Chat Updated", new ChatResource($chat), "chat");
    }

    //create function admin to join chat
    public function joinChat(Request $request, GroupChat $chat)
    {
        //check if user is already in chat
        $user = Auth::user();

        $chatUser = ChatUser::where('user_id', $user->id)->where('chat_id', $chat->id)->first();
        if ($chatUser) {
            return back()->with('message', 'You are already in this chat');
        }
        $chat->users()->create([
            'user_id' => $user->id,
        ]);
        return back()->with('message', 'Joined Chat Successfully.');
    }

    public function deleteMessage(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'isGroup' => 'required|boolean'
        ]);

        if ($data['isGroup']) {
            $message = Message::find($data['id']);
            $message->delete();
        } else {
            $message = ManagerChatMessage::find($data['id']);
            $message->delete();
        }

        return redirect()->back()->with('success', 'Message Deleted Successfully');
    }
}