<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Models\Embedding;

/**
 * Interface for Embedding repository.
 */
interface EmbeddingRepositoryInterface
{
    /**
     * Find an embedding by its unique identifier scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return Embedding|null
     */
    public function findById(string $id, string $projectId): ?Embedding;

    /**
     * Create a new embedding in the database.
     *
     * @param array<string, mixed> $data
     * @return Embedding
     */
    public function create(array $data): Embedding;

    /**
     * Update an existing embedding scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(string $id, string $projectId, array $data): bool;

    /**
     * Delete an embedding from the database scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return bool
     */
    public function delete(string $id, string $projectId): bool;

    /**
     * Get all embeddings scoped by project_id.
     *
     * @param string $projectId
     * @return Collection<int, Embedding>
     */
    public function allForProject(string $projectId): Collection;

    /**
     * Find embedding by chunk scoped by project.
     *
     * @param string $projectId
     * @param string $chunkId
     * @return Embedding|null
     */
    public function findByChunk(string $projectId, string $chunkId): ?Embedding;
}
