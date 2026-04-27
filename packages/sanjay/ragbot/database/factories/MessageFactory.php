<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Enums\MessageRole;
use Sanjay\Ragbot\Models\Conversation;
use Sanjay\Ragbot\Models\Message;
use Sanjay\Ragbot\Models\Project;

/**
 * Factory for the Message model.
 *
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory"s corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

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
            "conversation_id" => Conversation::factory(),
            "role" => MessageRole::User->value,
            "content" => $this->faker->sentence(),
        ];
    }
}
