<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanjay\Ragbot\Enums\DocumentStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rag_documents', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('The unique identifier for the document');
            $table->foreignUuid('project_id')->constrained('rag_projects')->cascadeOnDelete()->comment('The ID of the project the document belongs to');
            $table->string('name')->comment('The original name of the uploaded file');
            $table->string('file_path')->comment('The path where the file is stored');
            $table->string('mime_type')->comment('The MIME type of the file');
            $table->enum('status', array_column(DocumentStatus::cases(), 'value'))->default(DocumentStatus::Pending->value)->comment('The current processing status of the document');
            $table->text('error_message')->nullable()->comment('Any error message if the processing failed');
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->comment('Table storing documents uploaded by tenants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_documents');
    }
};
