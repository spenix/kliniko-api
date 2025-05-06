<?php

namespace Database\Seeders;

use App\Models\ProcedureType;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcedureTypeSeeder extends Seeder
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
                "name" => "Consultation",
                "description" => "This procedure is for consultation purposes only"
            ],
            [
                "name" => "X-Ray",
                "description" => "This procedure is for xray"
            ],
            [
                "name" => "Cleaning",
                "description" => "This procedure is for cleaning"
            ]
        ];

        Service::insert($data);
    }
}
