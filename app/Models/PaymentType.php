<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PaymentType extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = [];

    public static function rules($update = false, $id = null)
    {
        return [
            'name' => 'required|string',
            'is_cash' => 'required|boolean',
            'need_reference_details' => 'required|boolean'
        ];
    }
}
