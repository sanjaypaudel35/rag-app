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
        $driver = Schema::connection(config('database.default'))->getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('CREATE INDEX rag_document_chunks_content_fulltext_idx ON rag_document_chunks USING GIN (to_tsvector(\'english\', content))');
        } elseif ($driver === 'mysql') {
            Schema::table('rag_document_chunks', function (Blueprint $table) {
                $table->fullText('content');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::connection(config('database.default'))->getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS rag_document_chunks_content_fulltext_idx');
        } elseif ($driver === 'mysql') {
            Schema::table('rag_document_chunks', function (Blueprint $table) {
                $table->dropFullText(['content']);
            });
        }
    }
};
