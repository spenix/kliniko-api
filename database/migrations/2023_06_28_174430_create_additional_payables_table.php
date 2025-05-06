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
        Schema::create('additional_payables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->enum('type',['balance','others']);
            $table->string('description')->nullable();
            $table->decimal('amount', 16, 2)->default(0);
            $table->enum('is_delete', ['Y', 'N'])->default('N');
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_payables');
    }
};
