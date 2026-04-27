<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
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
        return [
            "id" => (string) Str::uuid(),
            "project_id" => Project::factory(),
            "llm_provider" => LlmProvider::OpenAI->value,
            "llm_api_key" => "sk-" . Str::random(32),
            "llm_model" => "gpt-4o-mini",
            "vector_store" => VectorStore::PgVector->value,
            "widget_enabled" => true,
        ];
    }
}
