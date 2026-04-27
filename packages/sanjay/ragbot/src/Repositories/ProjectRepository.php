<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Sanjay\Ragbot\Models\Project;

/**
 * Repository for managing Project entities.
 */
class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    /**
     * Create a new project repository instance.
     *
     * @param Project $model
     */
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    /**
     * Find a project by its unique identifier.
     *
     * @param string $id
     * @param string|null $projectId Unused for Project model
     * @return Project|null
     */
    public function findById(string $id, ?string $projectId = null): ?Project
    {
        return $this->model->find($id);
    }

    /**
     * Update an existing project.
     *
     * @param string $id
     * @param array<string, mixed> $data
     * @param string|null $projectId Unused for Project model
     * @return bool
     */
    public function update(string $id, array $data, ?string $projectId = null): bool
    {
        $record = $this->findById($id);

        if (!$record) {
            return false;
        }

        return $record->update($data);
    }

    /**
     * Delete a project from the database.
     *
     * @param string $id
     * @param string|null $projectId Unused for Project model
     * @return bool
     */
    public function delete(string $id, ?string $projectId = null): bool
    {
        $record = $this->findById($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    /**
     * Get all projects.
     *
     * @return Collection<int, Project>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find a project by its unique API key.
     *
     * @param string $apiKey
     * @return Project|null
     */
    public function findByApiKey(string $apiKey): ?Project
    {
        return $this->model
            ->where("api_key", $apiKey)
            ->where("is_active", true)
            ->first();
    }
}
