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
        Schema::create('rag_projects', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('The unique identifier for the project');
            $table->string('name')->comment('The name of the project');
            $table->string('slug')->unique()->comment('The URL-friendly slug for the project');
            $table->string('api_key')->unique()->index()->comment('The unique API key for project integration');
            $table->boolean('is_active')->default(true)->comment('Whether the project is currently active');
            $table->json('settings')->nullable()->comment('Additional project settings stored as JSON');
            $table->timestamps();

            $table->comment('Table storing the main project (tenant) information');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_projects');
    }
};
