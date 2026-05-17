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
            $table->string('widget_color')->default('#3b82f6');
            $table->string('widget_title')->default('Chat with us');
            $table->string('widget_logo')->nullable();
            $table->string('widget_position')->default('right'); // left, right
            $table->boolean('widget_full_page')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rag_project_settings', function (Blueprint $table) {
            $table->dropColumn([
                'widget_color',
                'widget_title',
                'widget_logo',
                'widget_position',
                'widget_full_page',
            ]);
        });
    }
};
