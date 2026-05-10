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
            if (Schema::hasColumn('rag_project_settings', 'embedding_provider')) {
                $table->dropColumn('embedding_provider');
            }
            if (Schema::hasColumn('rag_project_settings', 'embedding_provider_custom_name')) {
                $table->dropColumn('embedding_provider_custom_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->string('embedding_provider')->nullable();
            $table->string('embedding_provider_custom_name')->nullable();
        });
    }
};
