<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

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
}
