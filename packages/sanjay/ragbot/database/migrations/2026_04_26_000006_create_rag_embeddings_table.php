<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rag_embeddings', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('The unique identifier for the embedding');
            $table->foreignUuid('project_id')->constrained('rag_projects')->cascadeOnDelete()->comment('The ID of the project the embedding belongs to');
            $table->foreignUuid('chunk_id')->constrained('rag_document_chunks')->cascadeOnDelete()->comment('The ID of the chunk this embedding represents');
            
            // Default to JSON for MySQL/SQLite. PgVector handled via separate logic or raw SQL if needed.
            $table->json('vector')->comment('The vector representation of the chunk (JSON fallback)');
            
            $table->timestamps();

            $table->index(['project_id', 'chunk_id']);
            $table->comment('Table storing vector embeddings for document chunks');
        });

        // If using PostgreSQL, we might want to cast the column to 'vector' type later or here if extension exists.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_embeddings');
    }
};
