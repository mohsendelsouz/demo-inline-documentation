<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TruckMaintenanceResource extends JsonResource
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
            'truck' => new TruckResource($this->whenLoaded('truck')),
            'type' => $this->type,
            'service_type' => $this->service_type,
            'millage' => $this->millage,
            'amount' => $this->amount,
            'comment' => $this->comment,
        ];
    }
}
