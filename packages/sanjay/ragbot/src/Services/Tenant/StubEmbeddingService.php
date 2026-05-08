<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Models\Project;

class StubEmbeddingService implements EmbeddingInterface
{
    /**
     * Generates vector embeddings for text chunks.
     *
     * @return array<float>
     */
    public function embed(string $text, ?Project $project = null): array
    {
        $dimensions = config('ragbot.embedding.dimensions', 1536);
        $vector = [];

        for ($i = 0; $i < $dimensions; $i++) {
            $vector[] = (float) (mt_rand() / mt_getrandmax() * 2 - 1);
        }

        return $vector;
    }
}
