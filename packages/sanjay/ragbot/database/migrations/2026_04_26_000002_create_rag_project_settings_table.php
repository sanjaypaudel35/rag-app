<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Enums\VectorStore;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rag_project_settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('The unique identifier for the settings');
            $table->foreignUuid('project_id')->unique()->constrained('rag_projects')->cascadeOnDelete()->comment('The ID of the project these settings belong to');
            $table->enum('llm_provider', array_column(LlmProvider::cases(), 'value'))->default(LlmProvider::OpenAI->value)->comment('The AI provider used for LLM operations');
            $table->string('llm_api_key')->nullable()->comment('The API key for the LLM provider');
            $table->string('llm_model')->nullable()->comment('The specific model name for the LLM');
            $table->enum('vector_store', array_column(VectorStore::cases(), 'value'))->default(VectorStore::PgVector->value)->comment('The storage driver used for vector embeddings');
            $table->boolean('widget_enabled')->default(true)->comment('Whether the chat widget is enabled for this project');
            $table->timestamps();

            $table->comment('Table storing configuration settings for each project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_project_settings');
    }
};
