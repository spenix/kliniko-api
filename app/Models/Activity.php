<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Activity extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "control_no",
        "patient_id",
        "rc_notes",
        "branch_id",
        "clinic_id",
        "is_delete",
        "is_dentist_required",
        "dental_assistant"
    ];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
    */
    public static function rules($update = false, $id = null)
    {
        return [
            'patient_id' => 'required',
        ];
    }

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function services()
    {
        return $this->hasMany(ActivityService::class)->leftJoin('services', 'services.id', 'activity_services.service_id')
            ->leftJoin('users', 'users.id', 'activity_services.added_by')
            ->selectRaw('activity_services.*, users.name as added_by, services.name as service_name');
    }

    public function payments()
    {
        return $this->hasMany(ActivityPayment::class);
    }

    public function payment_values()
    {
        return $this->hasMany(ActivityPayment::class)->pluck('id', 'amount');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function discounts()
    {
        return $this->hasMany(ActivityDiscount::class);
    }

    public function additional_payables()
    {
        return $this->hasMany(AdditionalPayable::class);
    }

    public function discount_values()
    {
        return $this->hasMany(ActivityDiscount::class)->pluck('id', 'discount_amount');
    }
}
