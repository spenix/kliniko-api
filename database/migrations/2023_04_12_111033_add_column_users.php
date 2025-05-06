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
                if(!Schema::hasColumns('users', ['firstname', 'lastname', 'middlename'])){
                    $table->string('firstname')->after('name');
                    $table->string('lastname')->after('firstname');
                    $table->string('middlename')->nullable()->after('lastname');
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
                if(Schema::hasColumns('users', ['firstname', 'lastname', 'middlename'])){
                    $table->dropColumn('firstname');
                    $table->dropColumn('lastname');
                    $table->dropColumn('middlename');
                } 
            }); 
        }
    }
};
