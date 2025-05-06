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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('name');
            $table->enum('is_fixed_amount', ['Y', 'N'])->default('Y');
            $table->decimal('discount_rate', 8, 3)->default(0.000);
            $table->decimal('discount_amount', 8, 3)->default(0.000);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('branch_id')->references('id')->on('branches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
};
