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
        Schema::create("ragbot_users", function (Blueprint $table) {
            $table->uuid("id")->primary()->comment("The unique identifier for the user");
            $table->foreignUuid("project_id")->constrained("rag_projects")->cascadeOnDelete()->comment("The ID of the project the user belongs to");
            $table->string("name")->comment("The name of the user");
            $table->string("email")->comment("The email address of the user");
            $table->string("password")->comment("The hashed password for the user");
            $table->rememberToken();
            $table->timestamps();

            $table->unique(["project_id", "email"]);
            $table->comment("Table storing tenant-specific users (Authenticatable)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("ragbot_users");
    }
};
