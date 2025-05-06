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
        Schema::table('activity_services', function (Blueprint $table) {
            $table->enum('is_voided',['Y','N'])->default('N')->after('is_delete');
            $table->string('voided_remarks')->nullable()->after('is_voided');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_services', function (Blueprint $table) {
            $table->dropColumn(['is_voided','voided_remarks']);
        });
    }
};
