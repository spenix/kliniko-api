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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string("first_name");
            $table->string("middle_name")->nullable();
            $table->string("last_name");
            $table->string("address_line1");
            $table->string("address_line2")->nullable();
            $table->string("address_line3")->nullable();
            $table->string("email_address")->nullable();
            $table->date("birth_date");
            $table->string("height")->nullable();
            $table->string("weight")->nullable();
            $table->enum("sex",["male","female"]);
            $table->enum("civil_status",["single","married","complicated"])->nullable();
            $table->string("job_title")->nullable();
            $table->string("contact_no");
            $table->string("fb_account")->nullable();
            $table->string("twitter_account")->nullable();
            $table->string("instagram_account")->nullable();
            $table->string("linkedin_account")->nullable();
            $table->string("nationality")->nullable();
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
        Schema::dropIfExists('doctors');
    }
};
