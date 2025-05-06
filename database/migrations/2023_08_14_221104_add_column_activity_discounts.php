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
        if (Schema::hasTable('activity_discounts')) {
            Schema::table('activity_discounts', function (Blueprint $table) {
                if (Schema::hasColumns('activity_discounts', ['discount_id'])) {
                    $table->unsignedBigInteger('discount_id')->nullable()->change();
                }
                if (!Schema::hasColumns('activity_discounts', ['name', 'is_fixed_amount', 'discount_rate'])) {
                    $table->string('name')->nullable()->after('id_number');
                    $table->enum('is_fixed_amount', ['Y', 'N'])->nullable()->after('name');
                    $table->decimal('discount_rate', 8, 3)->default(0.000)->after('is_fixed_amount');
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
        if (Schema::hasTable('activity_discounts')) {
            Schema::table('activity_discounts', function (Blueprint $table) {
                if (Schema::hasColumns('activity_discounts', ['discount_id'])) {
                    $table->unsignedBigInteger('discount_id')->change();
                }
                if (Schema::hasColumns('activity_discounts', ['name', 'is_fixed_amount', 'discount_rate'])) {
                    $table->dropColumn(['name', 'is_fixed_amount', 'discount_rate']);
                }
            });
        }
    }
};
