<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagerChatResource extends JsonResource
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
            'user' => $this->user,
            "isGroup" => false,
            'manager' => $this->manager,
            'accepted' => $this->accepted ? true : false,
            'isUserInChat' => $this->isUserInChat(),
            "lastMessage" => new ManagerChatMessageResource($this->getLastMessage()),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at,
        ];
    }
}
