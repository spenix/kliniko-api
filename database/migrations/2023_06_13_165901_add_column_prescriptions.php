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
        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                if (!Schema::hasColumns('prescriptions', ['activity_id'])) {
                    $table->unsignedBigInteger('activity_id')->nullable()->after('doctor_id');
                    $table->foreign('activity_id')->references('id')->on('activities');
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
        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                if (Schema::hasColumns('prescriptions', ['activity_id'])) {
                    $table->dropForeign(['activity_id']);
                    $table->dropColumn('activity_id');
                }
            });
        }
    }
};
