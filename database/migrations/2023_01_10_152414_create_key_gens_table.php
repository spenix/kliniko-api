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
        Schema::create('key_gens', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->unsignedBigInteger('branch_id');
            $table->integer('year_month');
            $table->char('prefix',10);
            $table->unsignedBigInteger('id'); 

            $table->primary(['branch_id', 'year_month', 'prefix', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('key_gens');
    }
};
