<?php

namespace Sanjay\Ragbot\Contracts\Services;

use Sanjay\Ragbot\Models\Project;

interface EmbeddingInterface
{
    /**
     * Generates vector embeddings for text.
     *
     * @return array<float>
     */
    public function embed(string $text, ?Project $project = null): array;
}
