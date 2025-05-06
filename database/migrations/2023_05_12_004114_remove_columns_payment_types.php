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
        if (Schema::hasTable('payment_types')) {
            Schema::table('payment_types', function (Blueprint $table) {
                if (Schema::hasColumns('payment_types', ['clinic_id', 'branch_id'])) {
                    $table->dropForeign(['clinic_id']);
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('clinic_id');
                    $table->dropColumn('branch_id');
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
        //
    }
};
