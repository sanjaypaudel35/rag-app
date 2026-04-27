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
     *
     * @param string $status
     * @return Collection
     */
    public function findByStatus(string $status): Collection;

    /**
     * Update document status.
     *
     * @param string $id
     * @param string $status
     * @param string|null $error
     * @return bool
     */
    public function updateStatus(string $id, string $status, ?string $error = null): bool;
}
