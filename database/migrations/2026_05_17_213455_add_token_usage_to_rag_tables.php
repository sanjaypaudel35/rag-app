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
            $table->unsignedBigInteger('total_input_tokens')->default(0);
            $table->unsignedBigInteger('total_output_tokens')->default(0);
            $table->decimal('total_cost', 15, 6)->default(0);
        });

        Schema::table('rag_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('input_tokens')->nullable();
            $table->unsignedBigInteger('output_tokens')->nullable();
        });

        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('total_input_tokens')->default(0);
            $table->unsignedBigInteger('total_output_tokens')->default(0);
            $table->decimal('total_cost', 15, 6)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->dropColumn(['total_input_tokens', 'total_output_tokens', 'total_cost']);
        });

        Schema::table('rag_messages', function (Blueprint $table) {
            $table->dropColumn(['input_tokens', 'output_tokens']);
        });

        Schema::table('rag_chatbots', function (Blueprint $table) {
            $table->dropColumn(['total_input_tokens', 'total_output_tokens', 'total_cost']);
        });
    }
};
