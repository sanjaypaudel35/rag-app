<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Document repository.
 */
interface DocumentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find documents by status.
     */
    public function findByStatus(string $status): Collection;

    /**
     * Update document status.
     */
    public function updateStatus(string $id, string $status, ?string $error = null): bool;
}
