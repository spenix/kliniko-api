<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class AdditionalPayable extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'activity_id',
        'type',
        'description',
        'amount',
        'is_delete'
    ];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
    */
    public static function rules($update = false, $id = null)
    {
        return [
            'activity_id' => 'required',
            'type' => 'required',
            'description' => 'required',
            'amount' => 'required'
        ];
    }

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
