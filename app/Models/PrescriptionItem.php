<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PrescriptionItem extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = [];

    public static function rules(): array
    {
        return [
            'prescription_id' => 'required|integer|exists:prescriptions,id',
            'quantity' => 'required|number',
            'name' => 'required|string|max:255',
            'uni_of_measurement' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
