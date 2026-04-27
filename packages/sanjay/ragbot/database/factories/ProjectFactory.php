<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Models\Project;

/**
 * Factory for the Project model.
 *
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * The name of the factory"s corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model"s default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company() . " RAG Bot";

        return [
            "id" => (string) Str::uuid(),
            "name" => $name,
            "slug" => Str::slug($name),
            "api_key" => "rb_" . Str::random(32),
            "is_active" => true,
            "settings" => null,
        ];
    }

    /**
     * Indicate that the project has a known test API key.
     *
     * @return self
     */
    public function test(): self
    {
        return $this->state(fn (array $attributes) => [
            "name" => "Test Project",
            "api_key" => "test_api_key_123",
        ]);
    }
}
