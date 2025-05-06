<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class BalanceHistory extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'activity_id',
        'patient_id',
        'is_payment',
        'before_balance',
        'amount',
        'after_balance',
        'description'
    ];

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
