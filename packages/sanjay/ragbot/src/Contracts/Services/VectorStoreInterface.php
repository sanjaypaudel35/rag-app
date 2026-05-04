<?php

namespace Sanjay\Ragbot\Contracts\Services;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Models\Project;

interface VectorStoreInterface
{
    /**
     * Stores vector embedding for a chunk.
     *
     * @param  array<float>  $vector
     */
    public function store(Project $project, string $chunkId, array $vector): void;

    /**
     * Searches for chunks similar to the query vector.
     *
     * @param  array<float>  $queryVector
     */
    public function search(Project $project, array $queryVector, int $topK = 5): Collection;
}
