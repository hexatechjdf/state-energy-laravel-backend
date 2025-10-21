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
        Schema::create('crm_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('location_id')->unique(); // The CRM's location ID
            $table->string('company_id')->nullable()->index();
            $table->text('access_token');
            $table->text('refresh_token');
            $table->integer('expires_in');
            $table->string('scope');
            $table->string('user_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_tokens');
    }
};
