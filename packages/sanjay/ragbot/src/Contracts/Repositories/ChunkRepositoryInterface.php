<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Models\Chunk;

/**
 * Interface for Chunk repository.
 */
interface ChunkRepositoryInterface
{
    /**
     * Find a chunk by its unique identifier scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return Chunk|null
     */
    public function findById(string $id, string $projectId): ?Chunk;

    /**
     * Create a new chunk in the database.
     *
     * @param array<string, mixed> $data
     * @return Chunk
     */
    public function create(array $data): Chunk;

    /**
     * Update an existing chunk scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(string $id, string $projectId, array $data): bool;

    /**
     * Delete a chunk from the database scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return bool
     */
    public function delete(string $id, string $projectId): bool;

    /**
     * Get all chunks scoped by project_id.
     *
     * @param string $projectId
     * @return Collection<int, Chunk>
     */
    public function allForProject(string $projectId): Collection;

    /**
     * Find chunks by document scoped by project.
     *
     * @param string $projectId
     * @param string $documentId
     * @return Collection<int, Chunk>
     */
    public function findByDocument(string $projectId, string $documentId): Collection;
}
