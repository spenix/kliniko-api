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
            if (!Schema::hasColumn('activity_services', 'remarks')) {
                $table->text('remarks')->nullable()->after('voided_remarks');
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
            if (Schema::hasColumn('activity_services', 'remarks')) {
                $table->dropColumn('remarks');
            }
        });
    }
};
