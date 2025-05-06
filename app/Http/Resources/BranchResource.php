<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'name' => $this->name,
            'patient_no_prefix' => $this->patient_no_prefix,
            'address' => $this->address,
            'contact_no' => $this->contact_no,
            'logo' => $this->logo,
            'clinic' => $this->clinic,
            'email' => $this->email,
            'fb_page' => $this->fb_page
        ];
    }
}
