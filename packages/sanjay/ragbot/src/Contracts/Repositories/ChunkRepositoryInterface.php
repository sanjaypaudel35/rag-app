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
     *
     * @param string $documentId
     * @return Collection
     */
    public function findByDocument(string $documentId): Collection;
}
