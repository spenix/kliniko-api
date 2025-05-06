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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('control_no');
            $table->unsignedBigInteger('patient_id');
            $table->text('rc_notes')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending','ongoing','done','cancelled'])->default('pending');
            $table->enum('is_paid', ['Y','N'])->default('N');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
};
