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
        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                if (!Schema::connection(env('DB_DATABASE_PORTAL'))->hasColumns('tasks', ['has_boold_transfusion', 'approximate_date', 'is_pregnant', 'taking_pills', 'taking_any_medications', 'if_has_med_specify'])) {
                    $table->enum('has_boold_transfusion', ['Y', 'N'])->default('N')->after('avatar');
                    $table->date('approximate_date')->nullable()->after('has_boold_transfusion');
                    $table->enum('is_pregnant', ['Y', 'N'])->default('N')->after('approximate_date');
                    $table->enum('taking_pills', ['Y', 'N'])->default('N')->after('is_pregnant');
                    $table->enum('taking_any_medications', ['Y', 'N'])->default('N')->after('taking_pills');
                    $table->string('if_has_med_specify')->nullable()->after('taking_any_medications');
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
        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                if (Schema::connection(env('DB_DATABASE_PORTAL'))->hasColumns('tasks', ['has_boold_transfusion', 'approximate_date', 'is_pregnant', 'taking_pills', 'taking_any_medications', 'if_has_med_specify'])) {
                    $table->dropColumn('has_boold_transfusion');
                    $table->dropColumn('approximate_date');
                    $table->dropColumn('is_pregnant');
                    $table->dropColumn('taking_pills');
                    $table->dropColumn('taking_any_medications');
                    $table->dropColumn('if_has_med_specify');
                }
            });
        }
    }
};
