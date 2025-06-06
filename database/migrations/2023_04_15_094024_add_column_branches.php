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
        if(Schema::hasTable('branches')){
            Schema::table('branches', function (Blueprint $table) {
                if(!Schema::hasColumns('branches', ['fb_page'])){
                    $table->string('fb_page')->nullable()->after('email');
                }
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
        if(Schema::hasTable('branches')){
            Schema::table('branches', function (Blueprint $table) {
                if(Schema::hasColumns('branches', ['fb_page'])){
                    $table->dropColumn('fb_page');
                }
            });
        }
    }
};
