<?php

namespace Sanjay\Ragbot\Database\Seeders;

use Illuminate\Database\Seeder;
use Sanjay\Ragbot\Models\Project;

/**
 * Seeder for the Ragbot package.
 */
class RagbotSeeder extends Seeder
{
    /**
     * Seed the package"s database.
     */
    public function run(): void
    {
        Project::factory()->test()->create();
    }
}
