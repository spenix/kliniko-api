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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string("patient_no");
            $table->string("first_name");
            $table->string("middle_name")->nullable();
            $table->string("last_name");
            $table->string("address_line1");
            $table->string("address_line2")->nullable();
            $table->string("address_line3")->nullable();
            $table->date("birth_date");
            $table->string("height")->nullable();
            $table->string("weight")->nullable();
            $table->enum("sex",["male","female"]);
            $table->enum("civil_status",["single","married","complicated"])->nullable();
            $table->string("occupation")->nullable();
            $table->string("religion")->nullable();
            $table->string("contact_no");
            $table->string("fb_account")->nullable();
            $table->string("nationality")->nullable();
            $table->string("general_physician")->nullable();
            $table->date("medical_last_visit")->nullable();
            $table->enum("has_serious_illness",["Y","N"])->default("N");
            $table->text("describe_illness")->nullable(); 
            $table->string("avatar")->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
