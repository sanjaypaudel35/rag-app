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
        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->string('llm_model_for_embedding')->nullable()->after('llm_model')->comment('The specific model name for embeddings');
            $table->string('llm_api_endpoint')->nullable()->after('llm_model_for_embedding')->comment('The API endpoint for LLM operations');
            $table->string('embedding_api_endpoint')->nullable()->after('llm_api_endpoint')->comment('The API endpoint for embedding operations');
            $table->unsignedBigInteger('total_tokens_used')->default(0)->after('widget_enabled')->comment('Total tokens consumed by this project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->dropColumn([
                'llm_model_for_embedding',
                'llm_api_endpoint',
                'embedding_api_endpoint',
                'total_tokens_used',
            ]);
        });
    }
};
