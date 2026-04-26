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
        Schema::create('rag_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('The unique identifier for the conversation');
            $table->foreignUuid('project_id')->constrained('rag_projects')->cascadeOnDelete()->comment('The ID of the project the conversation belongs to');
            $table->string('session_id')->index()->comment('The unique session identifier for the chat');
            $table->json('metadata')->nullable()->comment('Additional metadata for the conversation');
            $table->timestamps();

            $table->index(['project_id', 'session_id']);
            $table->comment('Table storing chat conversations initiated by users');
        });

        Schema::create('rag_messages', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('The unique identifier for the message');
            $table->foreignUuid('project_id')->constrained('rag_projects')->cascadeOnDelete()->comment('The ID of the project the message belongs to');
            $table->foreignUuid('conversation_id')->constrained('rag_conversations')->cascadeOnDelete()->comment('The ID of the conversation this message belongs to');
            $table->enum('role', ['user', 'assistant'])->comment('The role of the message sender');
            $table->text('content')->comment('The textual content of the message');
            $table->timestamps();

            $table->index(['project_id', 'conversation_id']);
            $table->comment('Table storing individual messages within a conversation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_messages');
        Schema::dropIfExists('rag_conversations');
    }
};
