<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('patient_diagram_data_records')) {
            Schema::table('patient_diagram_data_records', function (Blueprint $table) {
                if (!Schema::hasColumns('patient_diagram_data_records', ['color_code'])) {
                    $table->string('color_code')->nullable()->after('code_text');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('patient_diagram_data_records')) {
            Schema::table('patient_diagram_data_records', function (Blueprint $table) {
                if (Schema::hasColumns('patient_diagram_data_records', ['color_code'])) {
                    $table->dropColumn('color_code');
                }
            });
        }
    }
};
