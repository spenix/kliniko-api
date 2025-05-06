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
        Schema::create('patient_diagram_data_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagram_record_id');
            $table->enum('teeth_group', ['right1', 'left1', 'right2', 'left2', 'right3', 'left3', 'right4', 'left4']);
            $table->smallInteger('code');
            $table->enum('check_flag', ['Y', 'N']);
            $table->timestamps();
            $table->foreign('diagram_record_id')->references('id')->on('patient_diagram_records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_diagram_data_records');
    }
};
