<?php

namespace Sanjay\Ragbot\Contracts\Services;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Models\Project;

/**
 * Interface for keyword-based document retrieval.
 */
interface KeywordSearchInterface
{
    /**
     * Searches for chunks matching the query keywords.
     *
     * @param  array<string>  $documentIds
     */
    public function searchKeyword(Project $project, string $query, int $topK = 5, array $documentIds = []): Collection;
}
