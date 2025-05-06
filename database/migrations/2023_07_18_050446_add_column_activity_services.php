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
            if (!Schema::hasColumns('activity_services', ['is_commission_update', 'reason_to_update_commission'])) {
                $table->enum('is_commission_update', ['Y', 'N'])->default('N')->after('remarks');
                $table->text('reason_to_update_commission')->nullable()->after('is_commission_update');
            }
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
            if (Schema::hasColumns('activity_services', ['is_commission_update', 'reason_to_update_commission'])) {
                $table->dropColumns(['is_commission_update', 'reason_to_update_commission']);
            }
        });
    }
};
