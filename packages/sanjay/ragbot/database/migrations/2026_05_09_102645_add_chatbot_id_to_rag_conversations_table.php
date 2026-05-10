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
        Schema::table('rag_conversations', function (Blueprint $table) {
            $table->foreignUuid('chatbot_id')
                ->after('project_id')
                ->nullable()
                ->constrained('rag_chatbots')
                ->cascadeOnDelete()
                ->comment('The ID of the chatbot the conversation belongs to');

            $table->index(['project_id', 'chatbot_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_conversations', function (Blueprint $table) {
            $table->dropForeign(['chatbot_id']);
            $table->dropIndex(['project_id', 'chatbot_id', 'session_id']);
            $table->dropColumn('chatbot_id');
        });
    }
};
