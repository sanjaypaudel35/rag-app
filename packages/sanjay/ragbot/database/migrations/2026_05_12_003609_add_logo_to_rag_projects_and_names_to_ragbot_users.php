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
        Schema::table('rag_projects', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('name')->comment('The logo of the project');
        });

        Schema::table('ragbot_users', function (Blueprint $table) {
            $table->string('firstname')->nullable()->after('project_id')->comment('The first name of the user');
            $table->string('lastname')->nullable()->after('firstname')->comment('The last name of the user');
            $table->timestamp('email_verified_at')->nullable()->after('email')->comment('The timestamp when the user email was verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ragbot_users', function (Blueprint $table) {
            $table->dropColumn(['firstname', 'lastname', 'email_verified_at']);
        });

        Schema::table('rag_projects', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
