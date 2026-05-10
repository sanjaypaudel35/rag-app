<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Models\Chunk;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;

/**
 * Factory for the Chunk model.
 *
 * @extends Factory<Chunk>
 */
class ChunkFactory extends Factory
{
    /**
     * The name of the factory"s corresponding model.
     *
     * @var string
     */
    protected $model = Chunk::class;

    /**
     * Define the model"s default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'project_id' => Project::factory(),
            'document_id' => Document::factory(),
            'content' => $this->faker->paragraph(),
            'chunk_index' => 0,
            'token_count' => 100,
        ];
    }
}
