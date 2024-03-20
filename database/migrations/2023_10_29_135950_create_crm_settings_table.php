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
        Schema::create('crm_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_id')->nullable()->constrained('crms')->onDelete('cascade');
            $table->time('working_hours_start');
            $table->time('working_hours_end');
            $table->json('working_days');
            $table->integer('daily_cap')->default(0);
            $table->boolean('is_active')->default(false);
            $table->boolean('skip_after_workings')->default(false);
            $table->boolean('generate_email_if_missing')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_settings');
    }
};
