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
                if (Schema::hasColumns('patients', ['email_address', 'birth_date', 'sex'])) {
                    $table->string('email_address')->nullable()->change();
                    $table->date('birth_date')->nullable()->change();
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
