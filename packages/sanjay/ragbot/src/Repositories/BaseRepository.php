<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base repository class providing common data access patterns with project scoping.
 */
abstract class BaseRepository
{
    /**
     * Create a new repository instance.
     */
    public function __construct(protected Model $model)
    {
    }

    /**
     * Find a model by its unique identifier scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return Model|null
     */
    public function findById(string $id, string $projectId): ?Model
    {
        return $this->model
            ->where("id", $id)
            ->where("project_id", $projectId)
            ->first();
    }

    /**
     * Create a new model instance in the database.
     *
     * @param array<string, mixed> $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing model instance scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(string $id, string $projectId, array $data): bool
    {
        $record = $this->findById($id, $projectId);

        if (!$record) {
            return false;
        }

        return $record->update($data);
    }

    /**
     * Delete a model instance from the database scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return bool
     */
    public function delete(string $id, string $projectId): bool
    {
        $record = $this->findById($id, $projectId);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    /**
     * Get all records scoped by project_id.
     *
     * @param string $projectId
     * @return Collection<int, Model>
     */
    public function allForProject(string $projectId): Collection
    {
        return $this->model->where("project_id", $projectId)->get();
    }
}
