<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectOfferResource extends JsonResource
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
            'project' => new ProjectResource($this->project),
            'options' => $this->options,
            'contract_terms_conditions' => $this->contract_terms_conditions,
            'execution_plan' => $this->execution_plan,
            'scope_of_work' => $this->scope_of_work,
            'site_info' => $this->site_info,
            'offer_pdf' => $this->offer_pdf,
            'signature'=> $this->signature,
            'status' => $this->status,
            'manager_signature' => $this->manager_signature,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
