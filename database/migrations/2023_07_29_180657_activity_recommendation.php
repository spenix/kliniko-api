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
        if (!Schema::hasTable('activity_recommendations')) {
            Schema::create('activity_recommendations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('activity_id');
                $table->string('treatment');
                $table->string('next_visit_recom');
                $table->string('da_on_duty');
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->unsignedBigInteger('hide_by')->nullable();
                $table->enum('isDeleted', ['Y', 'N'])->default('N');
                $table->enum('isHidden', ['Y', 'N'])->default('N');
                $table->timestamps();
                $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
                $table->foreign('hide_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
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
        Schema::dropIfExists('activity_recommendations');
    }
};
