<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Models\Document;

/**
 * Interface for Document repository.
 */
interface DocumentRepositoryInterface
{
    /**
     * Find a document by its unique identifier scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return Document|null
     */
    public function findById(string $id, string $projectId): ?Document;

    /**
     * Create a new document in the database.
     *
     * @param array<string, mixed> $data
     * @return Document
     */
    public function create(array $data): Document;

    /**
     * Update an existing document scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(string $id, string $projectId, array $data): bool;

    /**
     * Delete a document from the database scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return bool
     */
    public function delete(string $id, string $projectId): bool;

    /**
     * Get all documents scoped by project_id.
     *
     * @param string $projectId
     * @return Collection<int, Document>
     */
    public function allForProject(string $projectId): Collection;

    /**
     * Find documents by status scoped by project.
     *
     * @param string $projectId
     * @param string $status
     * @return Collection<int, Document>
     */
    public function findByStatus(string $projectId, string $status): Collection;

    /**
     * Update document status.
     *
     * @param string $id
     * @param string $projectId
     * @param string $status
     * @param string|null $error
     * @return bool
     */
    public function updateStatus(string $id, string $projectId, string $status, ?string $error = null): bool;
}
