<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MachineMaintenanceResource extends JsonResource
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
            'date' => $this->date->format('Y-m-d'),
            'machine' => new MachineResource($this->whenLoaded('machine')),
            'type' => $this->type,
            'service_type' => $this->service_type,
            'hour' => $this->hour,
            'amount' => $this->amount,
            'comment' => $this->comment,
        ];
    }
}
