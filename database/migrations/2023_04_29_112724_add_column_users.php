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
        if(Schema::hasTable('users')){
            Schema::table('users', function (Blueprint $table) {
                if(!Schema::hasColumns('users', ['profile_path'])){
                    $table->string('profile_path')->nullable()->after('role');
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
        if(Schema::hasTable('users')){
            Schema::table('users', function (Blueprint $table) {
                if(Schema::hasColumns('users', ['profile_path'])){
                    $table->dropColumn('profile_path');
                } 
            }); 
        }
    }
};
