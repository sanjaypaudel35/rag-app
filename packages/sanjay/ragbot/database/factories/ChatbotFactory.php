<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Project;

class ChatbotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Chatbot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => $this->faker->words(2, true).' Bot',
            'api_key' => hash('sha256', 'rb_c_'.Str::random(60)),
            'total_tokens_used' => $this->faker->numberBetween(0, 100000),
            'total_conversations' => $this->faker->numberBetween(0, 500),
        ];
    }
}
