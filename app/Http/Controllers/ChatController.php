<?php

namespace App\Http\Controllers;

use App\Events\NotifyUser;
use App\Http\Resources\ChatResource;
use App\Http\Resources\ManagerChatResource;
use App\Http\Resources\MessageResource;
use App\Models\ChatUser;
use App\Models\GroupChat;
use App\Models\ManagerChat;
use App\Models\ManagerChatMessage;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification as ModelsNotification;


class ChatController extends Controller
{
    public function adminIndex(Request $request)
    {
        $chats = GroupChat::orderBy('updated_at', 'DESC')->get();
        $chatRequests = ManagerChat::where('accepted', false)->get();
        $managerChats = ManagerChat::where('accepted', true)->where('manager_id', auth()->user()->id)->get();

        return $this->jsonSuccess(200, 'Chats fetched successfully', [
            'chats' => ChatResource::collection($chats),
            'chatRequests' => ManagerChatResource::collection($chatRequests),
            'managerChats' => ManagerChatResource::collection($managerChats),
        ], 'manager_chat');
    }

    public function getChatMessages($id)
    {
        $messages = Message::where('group_chat_id', $id)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);



        return MessageResource::collection($messages->reverse());
    }

    public function getManagerChatMessages($id)
    {
        $messages = ManagerChatMessage::where('manager_chat_id', $id)->orderBy('created_at', 'DESC')->paginate(10);

        return MessageResource::collection($messages->reverse());
    }

    public function searchChats(Request $request)
    {
        $userSearch = $request->search;


        $chats = GroupChat::where('name', 'like', '%' . $request->search . '%')->get();

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
        $chats = GroupChat::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        $chatRequests = ManagerChat::where('accepted', false)->get();
        $managerChats = ManagerChat::where('user_id', $user->id)->get();


        return $this->jsonSuccess(200, 'Chats fetched successfully', [
            'chats' => ChatResource::collection($chats),
            'chatRequests' => ManagerChatResource::collection($chatRequests),
            'managerChats' => ManagerChatResource::collection($managerChats),
        ], 'manager_chat');
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

        return $this->jsonSuccess(200, 'Chats fetched successfully', [
            'chats' => ChatResource::collection($chats),
            'chatRequests' => ManagerChatResource::collection($chatRequests),
            'managerChats' => ManagerChatResource::collection($managerChats),
            'projectGroupByMonth' => $projectGroupByMonth,
            'lastRequests' => $lastRequests,
        ], 'manager_chat');
    }
    public function refresh(Request $request)
    {
        return back()->with('message', 'New Message from ' . $request->first_name . ' ' . $request->last_name);
    }

    public function store(Request $request, Project $project)
    {
        $chat = GroupChat::create([
            'name' => $project->title,
            'project_id' => $project->id,
        ]);
        $chat->users()->create([
            'user_id' => $project->user_id,
        ]);

        return $this->jsonSuccess(200, "Chat Created", new ChatResource($chat), "chat");
    }

    // create function to add users to a chat
    public function addUsers(Request $request, GroupChat $chat)
    {
        //check if user is in the chat
        $chatUser = ChatUser::where('user_id', $request->user_id)->where('group_chat_id', $chat->id)->first();
        if ($chatUser) {
            return $this->jsonSuccess(200, "User already in chat", null, "user");
        }
        //check if request has users
        $chat->users()->create([
            'user_id' => $request->user_id,
        ]);
        $notification = ModelsNotification::create([
            'user_id' => $request->user_id,
            'title' => "You Have Been Added To Chat..",
            'body' => "",
            'type' => "Chat",
        ]);
        broadcast(new NotifyUser($request->user_id, $notification))->toOthers();
        return $this->jsonSuccess(200, "User added to chat successfully", null, "user");
    }

    // create function to remove users from a chat
    public function removeUsers(Request $request, GroupChat $chat)
    {
        //check if request has users
        ChatUser::where('user_id', $request->user_id)->where('group_chat_id', $chat->id)->delete();
        return $this->jsonSuccess(200, "User removed from chat successfully", null, "user");
    }

    // create function to delete a chat
    public function delete(GroupChat $chat)
    {
        $chat->delete();
        return $this->jsonSuccess(200, "Chat Deleted", null, "chat");
    }

    // create function to get all chats
    public function index()
    {
        $user = Auth::user();
        $chats = GroupChat::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();


        $managerChats = ManagerChat::where('user_id', auth()->user()->id)->get();

        return $this->jsonSuccess(200, "User chats", ["group_chats" => ChatResource::collection($chats), "managerChats" => ManagerChatResource::collection($managerChats)], "chats");
    }

    // create function to get a single chat
    public function show(GroupChat $chat)
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

        $chatUser = ChatUser::where('user_id', $user->id)->where('group_chat_id', $chat->id)->first();
        if ($chatUser) {
            return $this->jsonError(400, "You are already in this chat", null, "chat");
        }
        $chat->users()->create([
            'user_id' => $user->id,
        ]);
        return  $this->jsonSuccess(200, "Joined Chat", new ChatResource($chat), "chat");
    }

    public function deleteMessage(Request $request)
    {
        $data = $request->validate([
            'id' => 'required'
        ]);

        if ($request->isGroup) {
            $message = Message::find($data['id']);
            $message->delete();
        } else {
            $message = ManagerChatMessage::find($data['id']);
            $message->delete();
        }
        return $this->jsonSuccess(200, "Message Deleted", null, "chat");
    }
}
