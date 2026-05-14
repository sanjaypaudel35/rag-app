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
        Schema::table('ragbot_users', function (Blueprint $table) {
            $table->string('profile_photo_path', 2048)->nullable()->after('email_verified_at')->comment('The path to the user\'s profile photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ragbot_users', function (Blueprint $table) {
            $table->dropColumn('profile_photo_path');
        });
    }
};
