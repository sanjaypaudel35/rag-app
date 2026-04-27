<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface;
use Sanjay\Ragbot\Models\Chunk;

/**
 * Repository for managing Chunk entities.
 */
class ChunkRepository extends BaseRepository implements ChunkRepositoryInterface
{
    /**
     * Create a new chunk repository instance.
     *
     * @param Chunk $model
     */
    public function __construct(Chunk $model)
    {
        parent::__construct($model);
    }

    /**
     * Find chunks by document scoped by project.
     *
     * @param string $projectId
     * @param string $documentId
     * @return Collection<int, Chunk>
     */
    public function findByDocument(string $projectId, string $documentId): Collection
    {
        return $this->model
            ->where("project_id", $projectId)
            ->where("document_id", $documentId)
            ->get();
    }
}
