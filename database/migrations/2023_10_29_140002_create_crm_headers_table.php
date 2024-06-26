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
        Schema::create('crm_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_id')->nullable()->constrained('crms')->onDelete('cascade');
            $table->string('header_name');
            $table->string('header_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_headers');
    }
};
