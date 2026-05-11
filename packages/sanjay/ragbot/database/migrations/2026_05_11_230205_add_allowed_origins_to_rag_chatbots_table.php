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
        Schema::table('rag_chatbots', function (Blueprint $table) {
            $table->json('allowed_origins')->nullable()->after('api_key')->comment('JSON array of allowed CORS origins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_chatbots', function (Blueprint $table) {
            $table->dropColumn('allowed_origins');
        });
    }
};
