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
                if (!Schema::hasColumns('activities', ['is_delete'])) {
                    $table->enum('is_delete', ['Y', 'N'])->default('N')->after('is_paid');
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
                if (Schema::hasColumns('activities', ['is_delete'])) {
                    $table->dropColumn('is_delete');
                }
            });
        }
    }
};
