<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityServiceResource extends JsonResource
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
            'activity_id' => $this->activity_id,
            'service_id' => $this->service_id,
            'service_name' => $this->service->name,
            'activity' => $this->activity,
            'amount' => $this->amount,
            'commission_amount' => $this->commission_amount,
            'reason_to_update_commission' => $this->reason_to_update_commission,
            'is_delete' => $this->is_delete,
            'is_voided' => $this->is_voided,
            'remarks' => $this->remarks,
            'voided_remarks' => $this->voided_remarks,
            'added_by' => $this->added_by
        ];
    }
}
