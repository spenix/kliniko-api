<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Attachment extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $guarded = [];

    public static function rules($update = false, $id = null)
    {
        return [
            'patient_id' => 'required|integer|exists:patients,id',
            'attachment_type' => 'required|in:general,xray,contract,progress',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
