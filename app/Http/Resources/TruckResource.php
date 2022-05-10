<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TruckResource extends JsonResource
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
            'millage' => $this->millage,
            'last_inspection' => $this->last_inspection ? $this->last_inspection->format('M j, Y') : null,
            'machine' => new MachineResource($this->whenLoaded('machine')),
        ];
    }
}
