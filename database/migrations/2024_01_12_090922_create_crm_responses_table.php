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
        Schema::create('crm_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_id')->constrained('crms')->onDelete('cascade');
            $table->string('response_type'); // Например, 'success', 'duplicate', 'error'
            $table->text('response_path')->nullable(); // Путь к значению в JSON ответе, используя точечную нотацию для вложенности
            $table->string('expected_value')->nullable(); // Ожидаемое значение для сопоставления
            $table->string('expected_type')->default('string');
            $table->boolean('is_empty')->default(false); // Индикатор, ожидается ли пустой ответ для успеха
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_responses');
    }
};
