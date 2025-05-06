<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ExpenseType extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    // protected $fillable = [
    //     "name",
    //     "description",
    //     "branch_id"
    // ];


    public static function rules($update = false, $id = null)
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'branch_id' => 'required|integer|exists:branches,id',
            'clinic_id' => 'required|integer|exists:clinics,id'
        ];
    }

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
