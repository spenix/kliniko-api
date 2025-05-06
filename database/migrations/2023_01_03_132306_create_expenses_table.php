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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_type_id')->nullable();
            $table->string('other')->nullable();
            $table->string('description')->nullable();
            $table->date('expense_date');
            $table->float('amount', 10,2)->default(0);
            $table->timestamps();

            $table->foreign('expense_type_id')->references('id')->on('expense_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
