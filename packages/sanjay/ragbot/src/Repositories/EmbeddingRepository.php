<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Contracts\Repositories\EmbeddingRepositoryInterface;
use Sanjay\Ragbot\Models\Embedding;

/**
 * Repository for managing Embedding entities.
 */
class EmbeddingRepository extends BaseRepository implements EmbeddingRepositoryInterface
{
    /**
     * Create a new embedding repository instance.
     */
    public function __construct(Embedding $model)
    {
        parent::__construct($model);
    }

    /**
     * Find embedding by chunk.
     */
    public function findByChunk(string $chunkId): ?Embedding
    {
        return $this->model
            ->where('chunk_id', $chunkId)
            ->first();
    }

    /**
     * Get embeddings with chunks for a project, optionally filtered by documents.
     *
     * @param  array<string>  $documentIds
     */
    public function getWithChunks(string $projectId, array $documentIds = []): Collection
    {
        $query = $this->model->where('project_id', $projectId)
            ->with('chunk');

        if (! empty($documentIds)) {
            $query->whereHas('chunk', function ($q) use ($documentIds) {
                $q->whereIn('document_id', $documentIds);
            });
        }

        return $query->get();
    }

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
    ): Collection {
        $query = $this->model->where('project_id', $projectId)
            ->with('chunk');

        if (! empty($documentIds)) {
            $query->whereHas('chunk', function ($q) use ($documentIds) {
                $q->whereIn('document_id', $documentIds);
            });
        }

        return $query->orderByRaw($orderByRaw, $bindings)
            ->take($limit)
            ->get();
    }
}
