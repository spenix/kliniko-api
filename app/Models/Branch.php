<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Branch extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
    */
    public static function rules($update = false, $id = null)
    {
        return [
            'name' => 'required',
            'patient_no_prefix' => 'required',
            'clinic_id' => 'required',
            'email' => 'required|string|email'
        ];
    }

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
