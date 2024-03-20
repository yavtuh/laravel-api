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
        Schema::create('crm_status_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_status_lead_id')->constrained('crm_status_leads')->onDelete('cascade');
            $table->string('remote_field');
            $table->string('field_type')->nullable();
            $table->string('another_value')->nullable();
            $table->boolean('is_start_date')->default(false);
            $table->boolean('is_end_date')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_status_fields');
    }
};
