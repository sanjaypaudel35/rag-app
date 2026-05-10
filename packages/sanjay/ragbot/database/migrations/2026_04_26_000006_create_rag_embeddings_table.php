<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');
        }

        Schema::create('rag_embeddings', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('The unique identifier for the embedding');
            $table->foreignUuid('project_id')->constrained('rag_projects')->cascadeOnDelete()->comment('The ID of the project the embedding belongs to');
            $table->foreignUuid('chunk_id')->constrained('rag_document_chunks')->cascadeOnDelete()->comment('The ID of the chunk this embedding represents');

            if (DB::getDriverName() === 'pgsql') {
                // For PostgreSQL, we use the vector type from pgvector extension
                // Note: The pgvector extension must be enabled in the database: CREATE EXTENSION IF NOT EXISTS vector;
                $table->vector('vector', 1536)->comment('The vector representation of the chunk (pgvector)');
            } else {
                // Fallback for MySQL/SQLite
                $table->json('vector')->comment('The vector representation of the chunk (JSON fallback)');
            }

            $table->timestamps();

            $table->index(['project_id', 'chunk_id']);
            $table->comment('Table storing vector embeddings for document chunks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_embeddings');
    }
};
