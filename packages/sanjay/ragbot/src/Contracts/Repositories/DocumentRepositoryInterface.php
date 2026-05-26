<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    /**
     * Get documents for a project with chunk counts.
     */
    public function getForProject(string $projectId): Collection;

    /**
     * Get document statistics for a project.
     *
     * @return array<string, int>
     */
    public function getStats(string $projectId): array;

    /**
     * Search and filter documents for a project with pagination.
     */
    public function searchForProject(
        string $projectId,
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 10
    ): LengthAwarePaginator;

    /**
     * Get recent documents for a project with limited statuses.
     *
     * @param  array<string>  $statuses
     */
    public function getRecentForProject(string $projectId, array $statuses = [], int $limit = 10): Collection;
}
