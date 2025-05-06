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
                if(!Schema::hasColumns('branches', ['address', 'contact_no', 'logo'])){
                    $table->string('address')->nullable()->after('patient_no_prefix');
                    $table->string('contact_no')->nullable()->after('address');
                    $table->string('logo')->nullable()->after('contact_no');
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
                if(Schema::hasColumns('branches', ['address', 'contact_no', 'logo'])){
                    $table->dropColumn('address');
                    $table->dropColumn('contact_no');
                    $table->dropColumn('logo');
                }
            });
        }
    }
};
