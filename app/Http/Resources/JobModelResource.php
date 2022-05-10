<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobModelResource extends JsonResource
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
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'invoice_no' => $this->invoice_no,
            'truck' => new TruckResource($this->whenLoaded('truck')),
            'machine' => new MachineResource($this->whenLoaded('machine')),
            'client' => $this->client,
            'payment_method' => $this->payment_method ?? '',
            'payment_received_at' => $this->payment_received_at ? $this->payment_received_at->format('Y-m-d') : null,
            'tip' => $this->tip,
            'referral_amount' => $this->referral_amount,
            'wow' => $this->wow,
            'wows' => $this->wows,
            'charity_donate' => $this->charity_donate,
            'manager_received' => $this->manager_received,
            'technician_commission' => $this->technician_commission,
            'sales_commission' => $this->sales_commission,
            'operational_manager_commission' => $this->operational_manager_commission,
            'general_manager_commission' => $this->general_manager_commission,
            'amount' => $this->amount,
            'truck_technician' => new UserResource($this->whenLoaded('truckTechnician')),
            'sales_person' => new UserResource($this->whenLoaded('salesPerson')),
            'operational_manager' => new ManagerResource($this->whenLoaded('operationalManager')),
            'general_manager' => new ManagerResource($this->whenLoaded('generalManager')),
            'technicians' => JobTechnicianResource::collection($this->whenLoaded('technicians')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'all_wows' => WowResource::collection($this->whenLoaded('allWows')),
            'donates' => CharityDonateResource::collection($this->whenLoaded('donates')),
            'referral' => new ReferralResource($this->whenLoaded('referral'))
        ];
    }
}
