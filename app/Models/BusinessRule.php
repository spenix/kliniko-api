<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class BusinessRule extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = [];
    protected $table = 'business_rules';
}
