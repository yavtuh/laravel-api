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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('crm_id')->nullable()->constrained('crms')->onDelete('set null');
            $table->foreignId('funnel_id')->nullable()->constrained('funnels')->onDelete('set null');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('utm')->nullable();
            $table->string('user_agent')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->text('extra')->nullable();
            $table->string('domain')->nullable();
            $table->string('country')->nullable();
            $table->json('sent_crms')->nullable();
            $table->timestamp('send_date')->nullable();
            $table->string('send_status')->nullable();
            $table->string('lead_status')->nullable();
            $table->string('send_result')->nullable();
            $table->string('uuid')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
