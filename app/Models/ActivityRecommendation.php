<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityRecommendation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function rules($update = false, $id = null)
    {
        return [ // app\Http\Controllers\API\ActivityRecommendations.php:38
            "activity_id" => "required|integer|exists:activities,id",
            "patient_id" => "required|integer|exists:patients,id",
            "id" => "nullable|integer",
            "treatment" => "required|string",
            "next_visit_recom" => "required|string",
            // "da_on_duty" => "required|string"
        ];
    }
}
