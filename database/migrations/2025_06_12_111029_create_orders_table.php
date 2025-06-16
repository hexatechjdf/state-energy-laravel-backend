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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Buyer info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('address');
            $table->string('zip_code');
            $table->string('city');

            // Financial info
            $table->decimal('total_amount', 10, 2);
            $table->decimal('loan_financed_amount', 10, 2)->default(0);
            $table->decimal('order_amount', 10, 2);

            $table->decimal('monthly_utility_bill', 10, 2)->nullable();
            $table->decimal('monthly_insurance_bill', 10, 2)->nullable();

            // Financing program name e.g. Mosaic / Renew Financial etc.
            $table->string('finance_provider')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
