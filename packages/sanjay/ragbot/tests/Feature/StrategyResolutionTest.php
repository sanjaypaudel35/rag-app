<?php

namespace Sanjay\Ragbot\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Enums\VectorStore;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;
use Sanjay\Ragbot\Services\Tenant\AnthropicLLMService;
use Sanjay\Ragbot\Services\Tenant\EmbeddingManager;
use Sanjay\Ragbot\Services\Tenant\LlmManager;
use Sanjay\Ragbot\Services\Tenant\MysqlVectorStoreService;
use Sanjay\Ragbot\Services\Tenant\OpenAIEmbeddingService;
use Sanjay\Ragbot\Services\Tenant\OpenAILLMService;
use Sanjay\Ragbot\Services\Tenant\StubLLMService;
use Sanjay\Ragbot\Services\Tenant\VectorStoreManager;
use Sanjay\Ragbot\Tests\TestCase;

class StrategyResolutionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_llm_manager_resolves_correct_provider_from_project_settings()
    {
        $project = Project::factory()->create();
        ProjectSetting::factory()->create([
            'project_id' => $project->id,
            'llm_provider' => LlmProvider::Anthropic,
        ]);

        $project->refresh();
        app()->instance('ragbot.project', $project);

        $manager = app(LlmManager::class);
        $resolved = $manager->resolve($project);

        $this->assertInstanceOf(AnthropicLLMService::class, $resolved);
    }

    /** @test */
    public function test_llm_manager_falls_back_to_config_default()
    {
        Config::set('ragbot.llm.default', 'stub');

        $project = Project::factory()->create();
        // No settings created

        $manager = app(LlmManager::class);
        $resolved = $manager->resolve($project);

        $this->assertInstanceOf(StubLLMService::class, $resolved);
    }

    /** @test */
    public function test_vector_store_manager_resolves_correct_driver()
    {
        $project = Project::factory()->create();
        ProjectSetting::factory()->create([
            'project_id' => $project->id,
            'vector_store' => VectorStore::MySql,
        ]);

        $project->refresh();
        app()->instance('ragbot.project', $project);

        $manager = app(VectorStoreManager::class);
        $resolved = $manager->resolve($project);

        $this->assertInstanceOf(MysqlVectorStoreService::class, $resolved);
    }

    /** @test */
    public function test_vector_store_manager_resolves_custom_driver()
    {
        $project = Project::factory()->create();
        ProjectSetting::factory()->create([
            'project_id' => $project->id,
            'vector_store' => VectorStore::Custom,
            'vector_store_custom_name' => 'pinecone',
        ]);

        $project->refresh();
        app()->instance('ragbot.project', $project);

        $manager = app(VectorStoreManager::class);

        $customDriver = \Mockery::mock(VectorStoreInterface::class);

        $manager->extend('pinecone', function () use ($customDriver) {
            return $customDriver;
        });

        $resolved = $manager->resolve($project);

        $this->assertSame($customDriver, $resolved);
    }

    /** @test */
    public function test_embedding_manager_resolves_correct_provider()
    {
        $project = Project::factory()->create();
        ProjectSetting::factory()->create([
            'project_id' => $project->id,
            'llm_provider' => LlmProvider::OpenAI,
        ]);

        $project->refresh();
        app()->instance('ragbot.project', $project);

        $manager = app(EmbeddingManager::class);
        $resolved = $manager->resolve($project);

        $this->assertInstanceOf(OpenAIEmbeddingService::class, $resolved);
    }

    /** @test */
    public function test_embedding_manager_resolves_custom_driver()
    {
        $project = Project::factory()->create();
        ProjectSetting::factory()->create([
            'project_id' => $project->id,
            'llm_model_for_embedding' => 'together',
        ]);

        $project->refresh();
        app()->instance('ragbot.project', $project);

        $manager = app(EmbeddingManager::class);

        $customDriver = \Mockery::mock(EmbeddingInterface::class);

        $manager->extend('together', function () use ($customDriver) {
            return $customDriver;
        });

        $resolved = $manager->resolve($project);

        $this->assertSame($customDriver, $resolved);
    }

    /** @test */
    public function test_openai_llm_service_formats_request_correctly()
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'Hello!']]]], 200),
        ]);

        $project = Project::factory()->create();
        ProjectSetting::factory()->create([
            'project_id' => $project->id,
            'llm_provider' => LlmProvider::OpenAI,
            'llm_api_key' => 'fake-key',
            'llm_model' => 'gpt-4o',
        ]);

        $project->refresh();
        app()->instance('ragbot.project', $project);

        $service = new OpenAILLMService($project);
        $response = $service->complete('Hi');

        $this->assertEquals('Hello!', $response->content);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/chat/completions' &&
                   $request['model'] === 'gpt-4o' &&
                   $request['messages'][0]['content'] === 'Hi';
        });
    }

    /** @test */
    public function test_anthropic_llm_service_formats_request_correctly()
    {
        Http::fake([
            '*anthropic.com*' => Http::response(['content' => [['text' => 'Hello from Claude!']]], 200),
        ]);

        $project = Project::factory()->create();
        ProjectSetting::factory()->create([
            'project_id' => $project->id,
            'llm_provider' => LlmProvider::Anthropic,
            'llm_api_key' => 'fake-anthropic-key',
            'llm_model' => 'claude-3-opus',
            'llm_api_endpoint' => null,
        ]);

        $project->refresh();
        app()->instance('ragbot.project', $project);

        $service = new AnthropicLLMService($project);
        $response = $service->complete('Hi Claude');

        $this->assertEquals('Hello from Claude!', $response->content);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.anthropic.com/v1/messages' &&
                   $request['model'] === 'claude-3-opus' &&
                   $request->hasHeader('x-api-key', 'fake-anthropic-key');
        });
    }
}
