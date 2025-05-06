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
        if (Schema::hasTable('activity_services')) {
            Schema::table('activity_services', function (Blueprint $table) {
                if (!Schema::hasColumns('activity_services', ['is_delete'])) {
                    $table->enum('is_delete', ['Y', 'N'])->default('N')->after('commission_amount');
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
        if (Schema::hasTable('activity_services')) {
            Schema::table('activity_services', function (Blueprint $table) {
                if (Schema::hasColumns('activity_services', ['is_delete'])) {
                    $table->dropColumn('is_delete');
                }
            });
        }
    }
};
