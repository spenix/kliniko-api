<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Service extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'name',
        'description',
        'is_other',
        'is_comm_based',
        'commission_rate',
        'branch_id',
        'is_comm_fixed_amount',
        'comm_fixed_amount'
    ];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
    */
    public static function rules($update = false, $id = null)
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'is_other' => 'required',
            'is_comm_based' => 'required',
            'branch_id' => 'required',
            'is_comm_fixed_amount' => 'required'
        ];
    }

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function activity_procedure()
    {
        return $this->hasMany(ActivityProcedure::class);
    }
}
