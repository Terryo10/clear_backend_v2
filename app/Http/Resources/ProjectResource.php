<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'title' => $this->title,
            'user_id' => $this->user_id,
            'description' => $this->description,
            'project_category_id' => $this->project_category_id,
            'street' => $this->street,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'budget' => $this->budget,
            'frequency' => $this->frequency,
            'start_date' =>   Carbon::parse($this->start_date),
            'end_date' =>   Carbon::parse($this->end_date),
            'key_factor' => $this->key_factor,
            'additionalRequirements' => $this->additionalRequirements,
            'status' => $this->getStatus(),
            'color' => $this->color,
            'images' => $this->images,
            'history' => $this->history,
            'service' => $this->service,
            'scopeFiles' => $this->scopeFiles,
            'transaction' => $this->transaction,
            'projectFeedBack' => $this->projectFeedBack,
            'offer' => $this->offer,
            'proposals' => ProposalResource::collection($this->proposals),
            'requestProposals' => RequestProposalResource::collection($this->requestProposals),

            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
