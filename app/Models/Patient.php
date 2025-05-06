<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Patient extends Model implements Auditable
{
    use HasFactory, AuditableTrait;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    // protected $fillable = [
    //     "patient_no",
    //     "first_name",
    //     "middle_name",
    //     "last_name",
    //     "address_line1",
    //     "address_line2",
    //     "address_line3",
    //     "birth_date",
    //     "height",
    //     "weight",
    //     "sex",
    //     "civil_status",
    //     "occupation",
    //     "religion",
    //     "contact_no",
    //     "email_address",
    //     "fb_account",
    //     "nationality",
    //     "general_physician",
    //     "medical_last_visit",
    //     "has_serious_illness",
    //     "describe_illness",
    //     "avatar",
    //     "branch_id"
    // ];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
    */
    public static function rules($update = false, $id = null)
    {

        return [
            'branch_id' => 'required|integer|exists:branches,id',
            'patient_no_prefix' => 'required|string',
            'first_name' => 'required',
            'last_name' => 'required',
            'address_line1' => 'required',
            'birth_date' => 'required|date',
            'contact_no' => 'required',
            'avatar' => 'nullable|image',
            'sex' => 'nullable|in:male,female',
            'email_address' => 'nullable|email',
            'civil_status' => 'nullable|in:single,married,complicated',
        ];
    }

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function balance_histories()
    {
        return $this->hasMany(BalanceHistory::class);
    }

    public function medical_conditions()
    {
        return $this->belongsToMany(MedicalCondition::class, 'patient_medical_conditions', 'patient_id', 'medical_condition_id');
    }
}
