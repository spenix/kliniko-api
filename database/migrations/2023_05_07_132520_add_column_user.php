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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumns('users', ['gender', 'birthdate', 'address', 'phone_num', 'mobile_num'])) {
                    $table->enum('gender', ['Male', 'Female'])->nullable()->after('profile_path');
                    $table->date('birthdate')->nullable()->after('gender');
                    $table->string('address')->nullable()->after('birthdate');
                    $table->string('mobile_num')->nullable()->after('address');
                    $table->string('phone_num')->nullable()->after('mobile_num');
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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumns('users', ['gender', 'birthdate', 'address', 'phone_num', 'mobile_num'])) {
                    $table->dropColumn('gender');
                    $table->dropColumn('birthdate');
                    $table->dropColumn('address');
                    $table->dropColumn('phone_num');
                    $table->dropColumn('mobile_num');
                }
            });
        }
    }
};
