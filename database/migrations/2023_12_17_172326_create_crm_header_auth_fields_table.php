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
        Schema::create('crm_header_auth_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_header_auth_id')->constrained('crm_header_auths')->onDelete('cascade');
            $table->string('header_name');
            $table->string('header_type');
            $table->string('header_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_header_auth_fields');
    }
};
