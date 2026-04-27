<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for Embedding repository.
 */
interface EmbeddingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find embedding by chunk.
     *
     * @param string $chunkId
     * @return Model|null
     */
    public function findByChunk(string $chunkId): ?Model;
}
