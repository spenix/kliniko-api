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
        Schema::table('patient_intra_oral_records', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_intra_oral_records', 'date_taken')) {
                $table->date('date_taken')->nullable()->after('updated_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_intra_oral_records', function (Blueprint $table) {
            if (Schema::hasColumn('patient_intra_oral_records', 'date_taken')) {
                $table->dropColumn('date_taken');
            }
        });
    }
};
