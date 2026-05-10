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
}
