<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface for User repository.
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a user by email scoped by project.
     */
    public function findByEmail(string $projectId, string $email): ?Model;

    /**
     * Create a new user for a specific project.
     *
     * @param  array<string, mixed>  $data
     */
    public function createForProject(string $projectId, array $data): Model;

    /**
     * Search and filter users for a project with pagination.
     */
    public function searchForProject(
        string $projectId,
        ?string $search = null,
        ?string $status = null,
        int $perPage = 10
    ): LengthAwarePaginator;
}
