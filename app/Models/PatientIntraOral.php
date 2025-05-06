<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PatientIntraOral extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = [];
    protected $table = 'patient_intra_oral_records';

    public static function rules($update = false, $id = null)
    {
        return [
            'row_num' => 'required|numeric',
            'column_num' => 'required|numeric',
            'imageFile' => 'required',
        ];
    }
}
