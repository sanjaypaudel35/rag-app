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
        Schema::table('rag_documents', function (Blueprint $table) {
            $table->uuid('processing_batch_id')->nullable()->after('status')->comment('ID of the Laravel Job Batch currently processing this document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_documents', function (Blueprint $table) {
            $table->dropColumn('processing_batch_id');
        });
    }
};
