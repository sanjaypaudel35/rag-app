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
     *
     * @return Model|null
     */
    public function findByProject(): ?Model;
}
