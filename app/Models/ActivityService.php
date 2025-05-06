<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ActivityService extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = [];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
    */
    public static function rules($update = false, $id = null)
    {
        return [
            'activity_id' => 'required',
            'service_id' => 'required',
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

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
