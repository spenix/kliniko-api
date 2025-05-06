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
                if (!Schema::hasColumns('activities', ['is_dentist_required'])) {
                    $table->enum('is_dentist_required', ['Y', 'N'])->default('N')->after('status');
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
                if (Schema::hasColumns('activities', ['is_dentist_required'])) {
                    $table->dropColumn(['is_dentist_required']);
                }
            });
        }
    }
};
