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
                if (!Schema::hasColumns('patient_diagram_data_records', ['code_text'])) {
                    $table->string('code_text')->nullable()->after('code');
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
                if (Schema::hasColumns('patient_diagram_data_records', ['code_text'])) {
                    $table->dropColumn('code_text');
                }
            });
        }
    }
};
