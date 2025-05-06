<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Prescription extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = [];

    public static function rules($update = false, $id = null)
    {
        return [
            'patient_id' => 'required|integer|exists:patients,id',
            'description' => 'required|string',
            'doctor_id' => 'required|integer|exists:doctors,id'
        ];
    }

    public function prescription_items()
    {
        return $this->hasMany(PrescriptionItem::class, 'prescription_id', 'id');
    }
}
