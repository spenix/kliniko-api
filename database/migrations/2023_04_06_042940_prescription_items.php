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
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prescription_id');
            $table->decimal('quantity', 11, 4);
            $table->string('uni_of_measurement');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('prescription_id')->references('id')->on('prescriptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prescription_items');
    }
};
