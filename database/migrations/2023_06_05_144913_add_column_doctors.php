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
        if (Schema::hasTable('doctors')) {
            Schema::table('doctors', function (Blueprint $table) {
                if (!Schema::hasColumns('doctors', ['branch_id'])) {
                    $table->unsignedBigInteger('branch_id')->after('avatar')->nullable();
                    $table->foreign('branch_id')->references('id')->on('branches');
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
        if (Schema::hasTable('doctors')) {
            Schema::table('doctors', function (Blueprint $table) {
                if (Schema::hasColumns('doctors', ['branch_id'])) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                }
            });
        }
    }
};
