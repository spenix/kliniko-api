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
        if (Schema::hasTable('activities')) {
            Schema::table('activities', function (Blueprint $table) {
                if (!Schema::hasColumns('activities', ['dental_assistant'])) {
                    $table->string('dental_assistant')->nullable()->after('doctor_id');
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
        if (Schema::hasTable('activities')) {
            Schema::table('activities', function (Blueprint $table) {
                if (Schema::hasColumns('activities', ['dental_assistant'])) {
                    $table->dropColumn('dental_assistant');
                }
            });
        }
    }
};
