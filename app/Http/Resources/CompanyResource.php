<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'email' => $this->email,
            'operation_manager_id' => $this->operation_manager_id,
            'general_manager_id' => $this->general_manager_id,
            'sales_person_id' => $this->sales_person_id,
            'wows_goal' => $this->wows_goal,
            'job_goal' => $this->job_goal,
            'production_goal' => $this->production_goal,
        ];
    }
}
