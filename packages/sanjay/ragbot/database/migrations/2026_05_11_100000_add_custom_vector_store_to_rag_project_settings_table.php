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
            $table->string('vector_store')->change()->comment('The storage driver used for vector embeddings');
            $table->string('vector_store_custom_name')->nullable()->after('vector_store')->comment('The name of the custom vector store driver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->enum('vector_store', ['mysql', 'pgvector'])->change();
            $table->dropColumn('vector_store_custom_name');
        });
    }
};
