<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Enums\VectorStore;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Factory for the ProjectSetting model.
 *
 * @extends Factory<ProjectSetting>
 */
class ProjectSettingFactory extends Factory
{
    /**
     * The name of the factory"s corresponding model.
     *
     * @var string
     */
    protected $model = ProjectSetting::class;

    /**
     * Define the model"s default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $defaultVectorStore = DB::getDriverName() === 'pgsql'
            ? VectorStore::PgVector->value
            : VectorStore::MySql->value;

        return [
            'id' => (string) Str::uuid(),
            'project_id' => Project::factory(),
            'llm_provider' => LlmProvider::OpenAI->value,
            'llm_api_key' => 'sk-'.Str::random(32),
            'llm_model' => 'gpt-4o-mini',
            'llm_model_for_embedding' => 'text-embedding-3-small',
            'llm_api_endpoint' => 'https://api.openai.com/v1',
            'embedding_api_endpoint' => 'https://api.openai.com/v1',
            'vector_store' => $defaultVectorStore,
            'widget_enabled' => true,
            'widget_color' => '#3b82f6',
            'widget_title' => 'Chat with us',
            'widget_position' => 'right',
            'widget_full_page' => false,
            'total_tokens_used' => 0,
            'total_input_tokens' => 0,
            'total_output_tokens' => 0,
            'total_cost' => 0,
            'total_conversations' => 0,
        ];
    }
}
