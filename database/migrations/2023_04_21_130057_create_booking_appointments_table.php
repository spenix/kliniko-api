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
        Schema::create('booking_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classification_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('title');
            $table->text('note')->nullable();
            $table->dateTime('date_from');
            $table->dateTime('date_to');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('classification_id')->references('id')->on('classifications');
            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_appointments');
    }
};
