<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface for Embedding repository.
 */
interface EmbeddingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find embedding by chunk.
     */
    public function findByChunk(string $chunkId): ?Model;

    /**
     * Get embeddings with chunks for a project, optionally filtered by documents.
     *
     * @param  array<string>  $documentIds
     */
    public function getWithChunks(string $projectId, array $documentIds = []): Collection;

    /**
     * Search embeddings for a project using raw order and limit.
     *
     * @param  array<string>  $documentIds
     * @param  array<mixed>  $bindings
     */
    public function searchWithChunks(
        string $projectId,
        array $documentIds,
        string $orderByRaw,
        array $bindings = [],
        int $limit = 5
    ): Collection;
}
