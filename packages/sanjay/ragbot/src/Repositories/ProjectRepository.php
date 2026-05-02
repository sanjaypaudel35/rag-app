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

    /**
     * Find a project by its slug.
     *
     * @param string $slug
     * @return Project|null
     */
    public function findBySlug(string $slug): ?Project
    {
        return $this->model
            ->where("slug", $slug)
            ->first();
    }
}
