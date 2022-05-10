<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'active' => $this->active,
            'production_goal' => $this->production_goal,
            'wows_goal' => $this->wows_goal,
            'job_goal' => $this->job_goal,
            'scorecard_goal' => $this->scorecard_goal,
            'roles' => UserRoleResource::collection($this->whenLoaded('roles'))
        ];
    }
}
