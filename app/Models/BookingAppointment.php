<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class BookingAppointment extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = [];

    public static function rules($update = false, $id = null)
    {
        return [
            'classification_id' => 'required|integer|exists:classifications,id',
            'patient_id' => 'nullable|integer|exists:patients,id',
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after:date_from',
        ];
    }
}
