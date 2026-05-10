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
            // Remove the custom LLM provider fields I added previously if they exist
            if (Schema::hasColumn('rag_project_settings', 'llm_provider_custom_name')) {
                $table->dropColumn('llm_provider_custom_name');
            }

            // Add dedicated embedding provider fields
            $table->string('embedding_provider')->nullable()->after('embedding_api_endpoint')->comment('The provider used for generating embeddings');
            $table->string('embedding_provider_custom_name')->nullable()->after('embedding_provider')->comment('The name of the custom embedding driver or class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->dropColumn(['embedding_provider', 'embedding_provider_custom_name']);
            $table->string('llm_provider_custom_name')->nullable();
        });
    }
};
