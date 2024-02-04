<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'project' => $this->project->id,
            'users' => $this->users,
            "isGroup" => true,
//            'messages' => MessageResource::collection($this->messages),
            "lastMessage" => new MessageResource($this->getLastMessage()),
            'isUserInChat' => $this->isUserInChat(),
            'accepted'  =>  true,
            'mainUser' => $this->getMainUser(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
