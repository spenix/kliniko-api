<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Doctor extends Model implements Auditable
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
    //     "job_title",
    //     "contact_no",
    //     "email_address",
    //     "fb_account",
    //     "twitter_account",
    //     "instagram_account",
    //     "linkedin_account",
    //     "nationality",
    //     "avatar",
    // ];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
    */
    public static function rules($update = false, $id = null)
    {

        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address_line1' => 'required|string',
            'birth_date' => 'required|date',
            'contact_no' => 'required',
            'avatar' => 'nullable|image',
            'email_address' => 'required|email',
            'license_no' => 'required',
            'sex' => 'in:male,female',
            'civil_status' => 'nullable|in:single,married,complicated',
            'branch_id' => 'nullable|exists:branches,id'
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
}
