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
        Schema::create('checklist_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('category_id')->nullable();
            $table->string('field_name');
            $table->string('file_name')->nullable();
            $table->string('crm_file_id')->nullable();
            $table->json('crm_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_uploads');
    }
};
