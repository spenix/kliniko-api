<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PatientDiagramRecord extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = [];
    protected $table = 'patient_diagram_records';

    public static function rules($update = false, $id = null)
    {

        return [
            'patient_id' => 'required|integer|exists:patients,id',
            'remarks' => 'nullable|string',
        ];
    }

    public function data_records()
    {
        return $this->hasMany(PatientDiagramDataRecord::class, 'diagram_record_id', 'id')->selectRaw('id, diagram_record_id, teeth_group, code, check_flag as value, code_text, color_code');
    }
}
