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
        Schema::table('rag_messages', function (Blueprint $table) {
            $table->string('model')->nullable()->after('content');
        });

        Schema::create('rag_chatbot_model_usage', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chatbot_id');
            $table->string('model');
            $table->unsignedBigInteger('input_tokens')->default(0);
            $table->unsignedBigInteger('output_tokens')->default(0);
            $table->decimal('cost', 15, 6)->default(0);
            $table->timestamps();

            $table->unique(['chatbot_id', 'model']);
            $table->foreign('chatbot_id')->references('id')->on('rag_chatbots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_chatbot_model_usage');

        Schema::table('rag_messages', function (Blueprint $table) {
            $table->dropColumn('model');
        });
    }
};
