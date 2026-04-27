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
     *
     * @param Embedding $model
     */
    public function __construct(Embedding $model)
    {
        parent::__construct($model);
    }

    /**
     * Find embedding by chunk scoped by project.
     *
     * @param string $projectId
     * @param string $chunkId
     * @return Embedding|null
     */
    public function findByChunk(string $projectId, string $chunkId): ?Embedding
    {
        return $this->model
            ->where("project_id", $projectId)
            ->where("chunk_id", $chunkId)
            ->first();
    }
}
