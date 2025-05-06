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
        if (Schema::hasTable('activity_payments')) {
            Schema::table('activity_payments', function (Blueprint $table) {
                if (!Schema::hasColumns('activity_payments', ['account_name', 'reference_num'])) {
                    $table->string('account_name')->nullable()->after('amount');
                    $table->string('reference_num')->nullable()->after('account_name');
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
        if (Schema::hasTable('activity_payments')) {
            Schema::table('activity_payments', function (Blueprint $table) {
                if (Schema::hasColumns('activity_payments', ['account_name', 'reference_num'])) {
                    $table->dropColumn('account_name');
                    $table->dropColumn('reference_num');
                }
            });
        }
    }
};
