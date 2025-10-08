<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('monthly_gas_bill')->nullable()->after('monthly_insurance_bill');
            $table->string('monthly_water_swerage_bill')->nullable()->after('monthly_gas_bill');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['monthly_gas_bill', 'monthly_water_swerage_bill']);
        });
    }
};
