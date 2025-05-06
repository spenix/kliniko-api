<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class KeyGen extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    public $timestamps = false;

    protected $fillable = [
        "prefix",
        "branch_id",
        "year_month"
    ];
}
