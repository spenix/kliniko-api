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
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->after('id')->nullable();
            $table->unsignedBigInteger('branch_id')->after('clinic_id')->nullable();
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['clinic_id','branch_id']);
        });
    }
};
