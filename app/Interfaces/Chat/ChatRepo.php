<?php

namespace App\Interfaces\Chat;

use App\Models\GroupChat;
use App\Models\ChatUser;
use App\Models\User;

class ChatRepo implements ChatRepoInterface
{


    public function getChat($id)
    {
        return GroupChat::find($id);
    }

    public function getChats()
    {
        return GroupChat::orderBy('updated_at', 'DESC')->get();
    }


    public function createChat($data)
    {
        return GroupChat::create($data);
    }

    public function updateChat($data, $id)
    {
        // TODO: Implement updateChat() method.
    }

    public function deleteChat($id)
    {
        return GroupChat::destroy($id);
    }

    //create function to add user to chat
    public function addUserToChat(GroupChat $chat, User $user)
    {
        return ChatUser::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);
    }

    //create function to remove user from chat
    public function removeUserFromChat(GroupChat $chat, User $user)
    {
        return $chat->users()->detach($user);
    }

    //create function to add users to chat
    public function addUsersToChat(GroupChat $chat, array $users)
    {
        foreach ($users as $user) {
            ChatUser::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id
            ]);
        }
        return true;
    }
}
