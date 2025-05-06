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
        if (Schema::hasTable('activity_recommendations')) {
            Schema::table('activity_recommendations', function (Blueprint $table) {
                if (Schema::hasColumns('activity_recommendations', ['da_on_duty'])) {
                    $table->dropColumn(['da_on_duty']);
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
        if (Schema::hasTable('activity_recommendations')) {
            Schema::table('activity_recommendations', function (Blueprint $table) {
                if (!Schema::hasColumns('activity_recommendations', ['da_on_duty'])) {
                    $table->string('da_on_duty')->after('next_visit_recom');
                }
            });
        }
    }
};
