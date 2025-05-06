<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'expense_type_id' => $this->expense_type_id,
            'expense_type' => $this->expense_type,
            'particular' => $this->particular,
            'other' => $this->other,
            'description' => $this->description,
            'expense_date' => $this->expense_date,
            'is_overhead' => $this->is_overhead,
            'amount' => $this->amount,
        ];
    }
}
