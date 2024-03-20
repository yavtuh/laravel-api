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
        Schema::create('crm_header_auths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_header_id')->nullable()->constrained('crm_headers')->onDelete('cascade');
            $table->string('base_url');
            $table->enum('method', ['GET', 'POST', 'PUT', 'DELETE']);
            $table->enum('content_type', ['json', 'form_params', 'query', 'multipart'])->default('json');
            $table->string('token_path')->nullable();
            $table->string('auth_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_header_auths');
    }
};
