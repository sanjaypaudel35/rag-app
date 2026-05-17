<?php

namespace Sanjay\Ragbot\Tests\Feature\Livewire\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Sanjay\Ragbot\Enums\LlmModel;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Livewire\Tenant\SettingsManager;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Tests\TestCase;

class SettingsManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_validates_llm_model_matches_provider()
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        Livewire::test(SettingsManager::class)
            ->set('settings.llm_provider', LlmProvider::OpenAI->value)
            ->set('settings.llm_model', LlmModel::Claude35Sonnet->value)
            ->call('save')
            ->assertHasErrors(['settings.llm_model']);

        Livewire::test(SettingsManager::class)
            ->set('settings.llm_provider', LlmProvider::OpenAI->value)
            ->set('settings.llm_model', LlmModel::Gpt4o->value)
            ->call('save')
            ->assertHasNoErrors(['settings.llm_model']);
    }

    /** @test */
    public function test_it_validates_embedding_model_matches_provider()
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        Livewire::test(SettingsManager::class)
            ->set('settings.llm_provider', LlmProvider::Anthropic->value)
            ->set('settings.llm_model_for_embedding', LlmModel::TextEmbedding3Small->value)
            ->call('save')
            ->assertHasErrors(['settings.llm_model_for_embedding']);

        Livewire::test(SettingsManager::class)
            ->set('settings.llm_provider', LlmProvider::OpenAI->value)
            ->set('settings.llm_model_for_embedding', LlmModel::TextEmbedding3Small->value)
            ->call('save')
            ->assertHasNoErrors(['settings.llm_model_for_embedding']);
    }

    /** @test */
    public function test_it_validates_llm_provider_is_from_enum()
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        Livewire::test(SettingsManager::class)
            ->set('settings.llm_provider', 'invalid-provider')
            ->call('save')
            ->assertHasErrors(['settings.llm_provider']);
    }
}
