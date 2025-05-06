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
        if(Schema::hasTable('prescription_items')){
            Schema::table('prescription_items', function (Blueprint $table) {
                if(!Schema::hasColumns('prescription_items', ['name'])){
                    $table->string('name')->after('prescription_id');
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
        if(Schema::hasTable('prescription_items')){
            Schema::table('prescription_items', function (Blueprint $table) {
                if(Schema::hasColumns('prescription_items', ['name'])){
                    $table->dropColumn('name');
                }
            });
        }
    }
};
