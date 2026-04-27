<?php

namespace Sanjay\Ragbot\Repositories;

use Sanjay\Ragbot\Contracts\Repositories\ProjectSettingRepositoryInterface;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Repository for managing ProjectSetting entities.
 */
class ProjectSettingRepository extends BaseRepository implements ProjectSettingRepositoryInterface
{
    /**
     * Create a new project setting repository instance.
     *
     * @param ProjectSetting $model
     */
    public function __construct(ProjectSetting $model)
    {
        parent::__construct($model);
    }

    /**
     * Find settings by project.
     *
     * @param string $projectId
     * @return ProjectSetting|null
     */
    public function findByProject(string $projectId): ?ProjectSetting
    {
        return $this->model
            ->where("project_id", $projectId)
            ->first();
    }
}
