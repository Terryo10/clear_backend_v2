<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProposalResource extends JsonResource
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
            'contractor' => $this->contractor,
            'project_id' => $this->project_id,
            'cost' => $this->cost,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'attachment' => $this->attachment,
            'description' => $this->description,
            'scope_of_work' => $this->scope_of_work,
            'site_info' => $this->site_info,
            'execution_plan' => $this->execution_plan,
            'contract_terms_conditions' => $this->contract_terms_conditions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
