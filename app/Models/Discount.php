<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function rules($update = false, $id = null)
    {
        return [
            'name' => 'required|string',
            'is_fixed_amount' => 'required|boolean',
            'branch_id' => 'required|integer|exists:branches,id',
        ];
    }
}
