<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable  implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public const ROLES = [
        'SYSTEM_ADMINISTRATOR' => 'SA',
        'ADMINISTRATOR' => 'AD',
        'OPERATIONAL_MANAGER' => 'OM',
        'OFFICER_IN_CHARGE' => 'OIC',
        'DENTAL_ASSISTANT' => 'DA',
    ];

    public static function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:SA,AD,OM,OIC,RC,DA',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function branches()
    {
        return $this->hasMany(UserBranch::class)->join('branches', 'branches.id', 'user_branches.branch_id')->leftJoin('clinics', 'clinics.id', 'branches.clinic_id')->selectRaw('user_branches.*, branches.*, clinics.name as clinic_name');
    }
}
