<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Interface for ProjectSetting repository.
 */
interface ProjectSettingRepositoryInterface
{
    /**
     * Find project settings by its unique identifier scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return ProjectSetting|null
     */
    public function findById(string $id, string $projectId): ?ProjectSetting;

    /**
     * Create new project settings in the database.
     *
     * @param array<string, mixed> $data
     * @return ProjectSetting
     */
    public function create(array $data): ProjectSetting;

    /**
     * Update existing project settings scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(string $id, string $projectId, array $data): bool;

    /**
     * Delete project settings from the database scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return bool
     */
    public function delete(string $id, string $projectId): bool;

    /**
     * Get all project settings scoped by project_id.
     *
     * @param string $projectId
     * @return Collection<int, ProjectSetting>
     */
    public function allForProject(string $projectId): Collection;

    /**
     * Find settings by project.
     *
     * @param string $projectId
     * @return ProjectSetting|null
     */
    public function findByProject(string $projectId): ?ProjectSetting;
}
