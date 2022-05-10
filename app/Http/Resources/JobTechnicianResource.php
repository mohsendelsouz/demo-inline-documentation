<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobTechnicianResource extends JsonResource
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
            'technician_id' => $this->technician_id,
            'technician' => new UserResource($this->whenLoaded('technician')),
            'default_percentage' => $this->default_percentage,
            'wow' => $this->wow,
            'commission' => $this->commission,
            'wow_type' => $this->wow_type,
            'tip' => $this->tip,
            'reliable' => $this->reliable,
            'team_player' => $this->team_player,
            'integrity' => $this->integrity,
            'great_communicator' => $this->great_communicator,
            'proactive' => $this->proactive,
            'avg_sc' => floor($this->avg_sc * 2) / 2,
            'comment' => $this->comment,
        ];
    }
}
