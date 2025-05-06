<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'control_no' => $this->control_no,
            'rc_notes' => $this->rc_notes,
            'remarks' => $this->remarks,
            'status' => $this->status,
            'is_dentist_required' => $this->is_dentist_required == 'Y',
            'is_paid' => $this->is_paid,
            'is_settle_with_balance' => $this->is_settle_with_balance,
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'dental_assistant' => $this->dental_assistant,
            'doctor_name' => !empty($this->doctor) ? $this->doctor->first_name . " " .  $this->doctor->last_name : '',
            'patient_name' => !empty($this->patient) ? $this->patient->first_name . " " .  $this->patient->last_name : '',
            'additional_commission' => $this->additional_commission,
            'additional_commission_remarks' => $this->additional_commission_remarks,
            'patient' => $this->patient,
            'payment' => $this->payments,
            'payment_type_values' => $this->payments->pluck("amount", "payment_type_id"),
            'discounts' => $this->discounts,
            'discount_values' => $this->discounts->pluck("discount_amount", "discount_id"),
            'services' => ActivityServiceResource::collection($this->services),
            'additional_payables' => $this->additional_payables,
            'created_at' => $this->created_at,
        ];
    }
}
