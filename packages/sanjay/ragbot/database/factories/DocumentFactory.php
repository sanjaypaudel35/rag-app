<?php

namespace Sanjay\Ragbot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Enums\DocumentStatus;

/**
 * Factory for the Document model.
 *
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * The name of the factory"s corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

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
            "name" => $this->faker->word() . ".pdf",
            "file_path" => "documents/" . $this->faker->uuid() . ".pdf",
            "mime_type" => "application/pdf",
            "status" => DocumentStatus::Pending->value,
            "error_message" => null,
        ];
    }
}
