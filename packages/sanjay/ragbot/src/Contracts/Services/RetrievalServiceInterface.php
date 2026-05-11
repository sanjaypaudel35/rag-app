<?php

namespace Sanjay\Ragbot\Contracts\Services;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Models\Project;

/**
 * Interface for document retrieval service.
 */
interface RetrievalServiceInterface
{
    /**
     * Retrieves the most relevant chunks for a given query using vector similarity.
     */
    public function retrieve(Project $project, string $query, int $topK = 5): Collection;
}
