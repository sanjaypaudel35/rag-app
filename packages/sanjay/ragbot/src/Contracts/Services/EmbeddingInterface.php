<?php

namespace Sanjay\Ragbot\Contracts\Services;

interface EmbeddingInterface
{
    /**
     * Generates vector embeddings for text.
     *
     * @return array<float>
     */
    public function embed(string $text): array;
}
