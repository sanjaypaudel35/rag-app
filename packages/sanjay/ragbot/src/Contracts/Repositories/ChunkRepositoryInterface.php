<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Chunk repository.
 */
interface ChunkRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find chunks by document.
     */
    public function findByDocument(string $documentId): Collection;

    /**
     * Search for chunks using full-text search.
     *
     * @param  array<string>  $documentIds
     */
    public function searchKeyword(string $projectId, string $query, array $documentIds = [], int $limit = 5): Collection;
}
