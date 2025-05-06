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
        if(Schema::hasTable('doctors')){
            Schema::table('doctors', function (Blueprint $table) {
                if(!Schema::hasColumns('doctors', ['license_no'])){
                    $table->string('license_no')->after('job_title');
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
        if(Schema::hasTable('doctors')){
            Schema::table('doctors', function (Blueprint $table) {
                if(Schema::hasColumns('doctors', ['license_no'])){
                    $table->dropColumn('license_no');
                }
            });
        }
    }
};
