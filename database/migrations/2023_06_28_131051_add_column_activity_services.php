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
            $table->unsignedBigInteger('added_by')->nullable()->after('voided_remarks');
            $table->foreign('added_by')->references('id')->on('users');
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
            $table->dropForeign('added_by');
            $table->dropColumn('added_by');
        });
    }
};
