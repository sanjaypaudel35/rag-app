<?php

namespace Sanjay\Ragbot\Repositories;

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
}
