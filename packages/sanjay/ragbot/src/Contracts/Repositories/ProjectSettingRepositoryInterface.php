<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for ProjectSetting repository.
 */
interface ProjectSettingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find settings by project.
     */
    public function findByProject(): ?Model;

    /**
     * Increment a column value for project settings.
     */
    public function incrementForProject(string $projectId, string $column, int $amount = 1): void;
}
