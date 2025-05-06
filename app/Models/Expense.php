<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Expense extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "expense_type_id",
        "particular",
        "other",
        "description",
        "expense_date",
        "amount",
        "branch_id",
        "is_overhead"
    ];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
    */
    public static function rules($update = false, $id = null)
    {
        return [
            'expense_type_id' => 'required',
            'particular' => 'required',
            'description' => 'required',
            'amount'   => 'required',
            'is_overhead' => 'required'
        ];
    }

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */

    public function expense_type()
    {
        return $this->belongsTo(ExpenseType::class);
    }
}
