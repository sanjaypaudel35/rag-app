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
     */
    public function __construct(ProjectSetting $model)
    {
        parent::__construct($model);
    }

    /**
     * Find settings by project.
     */
    public function findByProject(): ?ProjectSetting
    {
        return $this->model->first();
    }

    /**
     * Create or update a model instance.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     */
    public function updateOrCreate(array $attributes, array $values = []): ProjectSetting
    {
        /** @var ProjectSetting $record */
        $record = $this->model->updateOrCreate($attributes, $values);

        return $record;
    }

    /**
     * Find one model by attributes.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function findOneBy(array $attributes): ?ProjectSetting
    {
        /** @var ProjectSetting|null $record */
        $record = $this->model->where($attributes)->first();

        return $record;
    }

    /**
     * Increment a column value for project settings.
     */
    public function incrementForProject(string $projectId, string $column, int $amount = 1): void
    {
        $this->model->withoutGlobalScope('project')
            ->where('project_id', $projectId)
            ->increment($column, $amount);
    }
}
