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
     */
    public function __construct(Chunk $model)
    {
        parent::__construct($model);
    }

    /**
     * Find chunks by document.
     *
     * @return Collection<int, Chunk>
     */
    public function findByDocument(string $documentId): Collection
    {
        return $this->model
            ->where('document_id', $documentId)
            ->get();
    }
}
