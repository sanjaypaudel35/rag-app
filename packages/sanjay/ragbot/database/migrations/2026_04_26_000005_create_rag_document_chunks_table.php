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
        Schema::create('rag_document_chunks', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('The unique identifier for the chunk');
            $table->foreignUuid('project_id')->constrained('rag_projects')->cascadeOnDelete()->comment('The ID of the project the chunk belongs to');
            $table->foreignUuid('document_id')->constrained('rag_documents')->cascadeOnDelete()->comment('The ID of the document this chunk originated from');
            $table->text('content')->comment('The textual content of the chunk');
            $table->integer('chunk_index')->comment('The index of this chunk within the document');
            $table->integer('token_count')->nullable()->comment('The number of tokens in this chunk');
            $table->timestamps();

            $table->index(['project_id', 'document_id']);
            $table->comment('Table storing text chunks extracted from documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_document_chunks');
    }
};
