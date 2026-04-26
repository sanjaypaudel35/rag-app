<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanjay\Ragbot\Enums\MessageRole;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("rag_conversations", function (Blueprint ) {
            ->uuid("id")->primary()->comment("The unique identifier for the conversation");
            ->foreignUuid("project_id")->constrained("rag_projects")->cascadeOnDelete()->comment("The ID of the project the conversation belongs to");
            ->string("session_id")->index()->comment("The unique session identifier for the chat");
            ->json("metadata")->nullable()->comment("Additional metadata for the conversation");
            ->timestamps();

            ->index(["project_id", "session_id"]);
            ->comment("Table storing chat conversations initiated by users");
        });

        Schema::create("rag_messages", function (Blueprint ) {
            ->uuid("id")->primary()->comment("The unique identifier for the message");
            ->foreignUuid("project_id")->constrained("rag_projects")->cascadeOnDelete()->comment("The ID of the project the message belongs to");
            ->foreignUuid("conversation_id")->constrained("rag_conversations")->cascadeOnDelete()->comment("The ID of the conversation this message belongs to");
            ->string("role")->comment("The role of the message sender");
            ->text("content")->comment("The textual content of the message");
            ->timestamps();

            ->index(["project_id", "conversation_id"]);
            ->comment("Table storing individual messages within a conversation");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("rag_messages");
        Schema::dropIfExists("rag_conversations");
    }
};
