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
        Schema::create('crm_create_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_create_lead_id')->constrained('crm_create_leads')->onDelete('cascade');
            $table->string('local_field')->nullable();
            $table->string('remote_field');
            $table->string('field_type');
            $table->string('another_value')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_create_fields');
    }
};
