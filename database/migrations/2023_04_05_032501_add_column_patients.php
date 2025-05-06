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
        if(Schema::hasTable('patients')){
            Schema::table('patients', function (Blueprint $table) {
                if(!Schema::hasColumns('patients', ['twitter_account', 'instagram_account', 'linkedin_account'])){
                    $table->string("twitter_account")->nullable()->after('fb_account');
                    $table->string("instagram_account")->nullable()->after('twitter_account');
                    $table->string("linkedin_account")->nullable()->after('instagram_account');
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
        if(Schema::hasTable('patients')){
            Schema::table('patients', function (Blueprint $table) {
                if(Schema::hasColumns('patients', ['twitter_account', 'instagram_account', 'linkedin_account'])){
                    $table->dropColumn('twitter_account');
                    $table->dropColumn('instagram_account');
                    $table->dropColumn('linkedin_account');
                }
            });
        }
    }
};
