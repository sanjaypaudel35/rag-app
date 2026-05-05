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
        Schema::create('rag_chatbots', function (Blueprint $blueprint) {
            $blueprint->uuid('id')->primary();
            $blueprint->foreignUuid('project_id')->constrained('rag_projects')->onDelete('cascade');
            $blueprint->string('name');
            $blueprint->string('api_key')->unique();
            $blueprint->bigInteger('total_tokens_used')->default(0);
            $blueprint->integer('total_conversations')->default(0);
            $blueprint->timestamps();
        });

        Schema::create('rag_chatbot_documents', function (Blueprint $blueprint) {
            $blueprint->uuid('id')->primary();
            $blueprint->foreignUuid('chatbot_id')->constrained('rag_chatbots')->onDelete('cascade');
            $blueprint->foreignUuid('document_id')->constrained('rag_documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_chatbot_documents');
        Schema::dropIfExists('rag_chatbots');
    }
};
