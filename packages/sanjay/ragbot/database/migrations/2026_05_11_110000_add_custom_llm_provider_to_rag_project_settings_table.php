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
            $table->string('llm_provider')->change()->comment('The AI provider used for LLM operations');
            $table->string('llm_provider_custom_name')->nullable()->after('llm_provider')->comment('The name of the custom LLM/Embedding provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->enum('llm_provider', ['openai', 'anthropic', 'gemini', 'stub'])->change();
            $table->dropColumn('llm_provider_custom_name');
        });
    }
};
