<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Models\Conversation;
use Sanjay\Ragbot\Models\Project;

/**
 * Factory for the Conversation model.
 *
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * The name of the factory"s corresponding model.
     *
     * @var string
     */
    protected $model = Conversation::class;

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
            "session_id" => Str::random(16),
            "metadata" => null,
        ];
    }
}
