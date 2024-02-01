<?php

namespace App\Interfaces\Chat;

use App\Models\GroupChat;
use App\Models\User;

interface ChatRepoInterface
{
    public function getChat($id);
    public function getChats();
    public function createChat($data);
    public function updateChat($data, $id);
    public function deleteChat($id);
    public function addUserToChat(GroupChat $chat, User $user);
    public function removeUserFromChat(GroupChat $chat, User $user);
    public function addUsersToChat(GroupChat $chat, array $users);
}
