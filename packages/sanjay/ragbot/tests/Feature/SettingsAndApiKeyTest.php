<?php

namespace Sanjay\Ragbot\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Enums\VectorStore;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;
use Sanjay\Ragbot\Services\Tenant\ApiKeyService;
use Sanjay\Ragbot\Services\Tenant\ProjectSettingsService;
use Sanjay\Ragbot\Tests\TestCase;

class SettingsAndApiKeyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_project_settings_service_persists_all_fields()
    {
        $project = Project::factory()->create();
        $service = app(ProjectSettingsService::class);

        $data = [
            'llm_provider' => LlmProvider::Anthropic->value,
            'llm_api_key' => 'new-api-key',
            'llm_model' => 'claude-3-opus',
            'llm_model_for_embedding' => 'custom-embedding-model',
            'llm_api_endpoint' => 'https://api.anthropic.com/v1',
            'embedding_api_endpoint' => 'https://api.embeddings.com/v1',
            'vector_store' => VectorStore::PgVector->value,
            'widget_enabled' => false,
            'total_tokens_used' => 5000,
        ];

        $service->update($project, $data);

        $this->assertDatabaseHas('rag_project_settings', [
            'project_id' => $project->id,
            'llm_provider' => LlmProvider::Anthropic->value,
            'llm_model' => 'claude-3-opus',
            'vector_store' => VectorStore::PgVector->value,
            'widget_enabled' => 0,
            'total_tokens_used' => 5000,
        ]);
    }

    /** @test */
    public function test_project_settings_service_prevents_vector_store_change_after_initial_set()
    {
        $project = Project::factory()->create();
        $service = app(ProjectSettingsService::class);

        // Initial set to MySql
        $service->update($project, [
            'vector_store' => VectorStore::MySql->value,
            'llm_provider' => LlmProvider::OpenAI->value,
        ]);

        $settings = ProjectSetting::withoutGlobalScope('project')
            ->where('project_id', $project->id)
            ->first();

        $this->assertNotNull($settings, 'Settings should be created');
        $this->assertEquals(VectorStore::MySql, $settings->vector_store);

        // Attempt to change to PgVector
        $service->update($project, [
            'vector_store' => VectorStore::PgVector->value,
            'llm_provider' => LlmProvider::OpenAI->value,
        ]);

        // Should still be MySql
        $settings->refresh();
        $this->assertEquals(VectorStore::MySql, $settings->vector_store);
    }

    /** @test */
    public function test_api_key_service_regenerates_the_key()
    {
        $project = Project::factory()->create(['api_key' => 'old-key']);
        $service = app(ApiKeyService::class);

        $newKey = $service->regenerate($project);

        $this->assertNotEquals('old-key', $newKey);
        $this->assertEquals(hash('sha256', $newKey), $project->refresh()->api_key);
    }

    /** @test */
    public function test_old_api_key_no_longer_resolves_after_regeneration()
    {
        // This test depends on the middleware which resolves the project from the API key
        $project = Project::factory()->create(['api_key' => 'old-key']);
        $service = app(ApiKeyService::class);

        $this->assertEquals($project->id, Project::where('api_key', 'old-key')->first()?->id);

        $service->regenerate($project);

        $this->assertNull(Project::where('api_key', 'old-key')->first());
    }
}
