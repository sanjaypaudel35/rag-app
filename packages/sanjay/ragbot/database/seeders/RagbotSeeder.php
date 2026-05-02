<?php

namespace Sanjay\Ragbot\Database\Seeders;

use Illuminate\Database\Seeder;
use Sanjay\Ragbot\Models\Chunk;
use Sanjay\Ragbot\Models\Conversation;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Embedding;
use Sanjay\Ragbot\Models\Message;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;
use Sanjay\Ragbot\Models\RagbotUser;

/**
 * Seeder for the Ragbot package.
 */
class RagbotSeeder extends Seeder
{
    /**
     * Seed the package's database.
     */
    public function run(): void
    {
        // Create 2 Projects
        $projects = Project::factory()->count(2)->create();

        // One of the projects should be the test project for convenience
        $projects[0]->update([
            'name' => 'Test Project',
            'slug' => 'test-project',
            'api_key' => 'test_api_key_123',
        ]);

        foreach ($projects as $project) {
            // Create 1 ProjectSetting per project (unique constraint)
            ProjectSetting::factory()->create([
                'project_id' => $project->id,
            ]);

            // Create 2 RagbotUsers per project
            RagbotUser::factory()->count(2)->create([
                'project_id' => $project->id,
            ]);

            // Create 2 Documents per project
            $documents = Document::factory()->count(2)->create([
                'project_id' => $project->id,
            ]);

            foreach ($documents as $document) {
                // Create 2 Chunks per document
                $chunks = Chunk::factory()->count(2)->create([
                    'project_id' => $project->id,
                    'document_id' => $document->id,
                ]);

                foreach ($chunks as $chunk) {
                    // Create 1 Embedding per chunk
                    Embedding::factory()->create([
                        'project_id' => $project->id,
                        'chunk_id' => $chunk->id,
                    ]);
                }
            }

            // Create 2 Conversations per project
            $conversations = Conversation::factory()->count(2)->create([
                'project_id' => $project->id,
            ]);

            foreach ($conversations as $conversation) {
                // Create 2 Messages per conversation
                Message::factory()->count(2)->create([
                    'project_id' => $project->id,
                    'conversation_id' => $conversation->id,
                ]);
            }
        }
    }
}
