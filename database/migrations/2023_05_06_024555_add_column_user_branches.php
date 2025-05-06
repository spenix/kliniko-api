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
        if (Schema::hasTable('user_branches')) {
            Schema::table('user_branches', function (Blueprint $table) {
                if (!Schema::hasColumns('user_branches', ['role', 'created_by', 'updated_by'])) {
                    $table->enum('role', ['SA', 'AD', 'OM', 'OIC', 'RC', 'DA'])->default('RC')->after('branch_id');
                    $table->unsignedBigInteger('created_by')->nullable()->after('role');
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                    $table->foreign('created_by')->references('id')->on('users');
                    $table->foreign('updated_by')->references('id')->on('users');
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
        //
    }
};
