<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Models\Chunk;
use Sanjay\Ragbot\Models\Embedding;
use Sanjay\Ragbot\Models\Project;

/**
 * Factory for the Embedding model.
 *
 * @extends Factory<Embedding>
 */
class EmbeddingFactory extends Factory
{
    /**
     * The name of the factory"s corresponding model.
     *
     * @var string
     */
    protected $model = Embedding::class;

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
            "chunk_id" => Chunk::factory(),
            "vector" => array_fill(0, 1536, 0.1),
        ];
    }
}
