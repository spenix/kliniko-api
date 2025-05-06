<?php

namespace Database\Seeders;

use App\Models\MedicalCondition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                "name" => "Aids",
                "is_need_details" => "N"
            ],
            [
                "name" => "Arthritis",
                "is_need_details" => "N"
            ],
            [
                "name" => "Rheumatism",
                "is_need_details" => "N"
            ],
            [
                "name" => "Artificial Heart Valves",
                "is_need_details" => "N"
            ],
            [
                "name" => "Asthma",
                "is_need_details" => "N"
            ],
            [
                "name" => "Fainting",
                "is_need_details" => "N"
            ],
            [
                "name" => "Blood Disease",
                "is_need_details" => "N"
            ],
            [
                "name" => "Cancer",
                "is_need_details" => "N"
            ],
            [
                "name" => "Chemical",
                "is_need_details" => "N"
            ],
            [
                "name" => "Dependency",
                "is_need_details" => "N"
            ],
            [
                "name" => "Circulatory Problems",
                "is_need_details" => "N"
            ],
            [
                "name" => "Cough Blood",
                "is_need_details" => "N"
            ],
            [
                "name" => "Diabetes",
                "is_need_details" => "N"
            ],
            [
                "name" => "Epilepsy",
                "is_need_details" => "N"
            ],
            [
                "name" => "Mitral Valve Prolapse",
                "is_need_details" => "N"
            ],
            [
                "name" => "Headaches",
                "is_need_details" => "N"
            ],
            [
                "name" => "Heart Murmur",
                "is_need_details" => "N"
            ],
            [
                "name" => "Heart Problems",
                "is_need_details" => "Y"
            ],
            [
                "name" => "Hemophilia",
                "is_need_details" => "N"
            ],
            [
                "name" => "High Blood Pressure",
                "is_need_details" => "N"
            ],
            [
                "name" => "Hepatitis",
                "is_need_details" => "N"
            ],
            [
                "name" => "HIV",
                "is_need_details" => "N"
            ],
            [
                "name" => "Jaw Pain",
                "is_need_details" => "N"
            ],
            [
                "name" => "Kidney Disease",
                "is_need_details" => "N"
            ],
            [
                "name" => "Liver Disease",
                "is_need_details" => "N"
            ],
            [
                "name" => "Back Problem",
                "is_need_details" => "N"
            ],
            [
                "name" => "Pacemaker",
                "is_need_details" => "N"
            ],
            [
                "name" => "Psychiatric Care",
                "is_need_details" => "N"
            ],
            [
                "name" => "Radiation Treatment",
                "is_need_details" => "N"
            ],
            [
                "name" => "Respiratory Disease",
                "is_need_details" => "N"
            ],
            [
                "name" => "Rheumatic Fever",
                "is_need_details" => "N"
            ],
            [
                "name" => "Anemia",
                "is_need_details" => "N"
            ],
            [
                "name" => "Skin Rash",
                "is_need_details" => "N"
            ],
            [
                "name" => "Stroke",
                "is_need_details" => "N"
            ],
            [
                "name" => "Swelling of Feet/ankle",
                "is_need_details" => "N"
            ],
            [
                "name" => "Thyroid Problems",
                "is_need_details" => "N"
            ],
            [
                "name" => "Nervous Problems",
                "is_need_details" => "N"
            ],
            [
                "name" => "Tobacco Habbit",
                "is_need_details" => "N"
            ],
            [
                "name" => "Tonsilitis",
                "is_need_details" => "N"
            ],
            [
                "name" => "Ulcer",
                "is_need_details" => "N"
            ],
            [
                "name" => "Chemotherapy",
                "is_need_details" => "N"
            ],
            [
                "name" => "Scarlet Fever",
                "is_need_details" => "N"
            ],
        ];

        MedicalCondition::insert($data);
    }
}
