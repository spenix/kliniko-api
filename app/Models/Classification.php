<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Classification extends Model implements Auditable
{
    use HasFactory, AuditableTrait;
    protected $guarded = [];
    protected $table = 'classifications';

    public static function rules($update = false, $id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'isRequiredPatient' => 'required|boolean'
        ];
    }
}
