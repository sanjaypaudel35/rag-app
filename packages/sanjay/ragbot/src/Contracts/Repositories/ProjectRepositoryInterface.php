<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Models\Project;

/**
 * Interface for Project repository.
 */
interface ProjectRepositoryInterface
{
    /**
     * Find a project by its unique identifier.
     *
     * @param string $id
     * @return Project|null
     */
    public function findById(string $id): ?Project;

    /**
     * Create a new project in the database.
     *
     * @param array<string, mixed> $data
     * @return Project
     */
    public function create(array $data): Project;

    /**
     * Update an existing project.
     *
     * @param string $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a project from the database.
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;

    /**
     * Get all projects.
     *
     * @return Collection<int, Project>
     */
    public function all(): Collection;

    /**
     * Find a project by its unique API key.
     *
     * @param string $apiKey
     * @return Project|null
     */
    public function findByApiKey(string $apiKey): ?Project;
}
