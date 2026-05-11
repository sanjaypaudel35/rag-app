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
            $table->integer('rate_limit_per_minute')->default(60)->after('allowed_origins')->comment('Total requests per minute for this chatbot');
            $table->integer('session_rate_limit_per_minute')->default(10)->after('rate_limit_per_minute')->comment('Requests per minute per session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_chatbots', function (Blueprint $table) {
            $table->dropColumn(['rate_limit_per_minute', 'session_rate_limit_per_minute']);
        });
    }
};
