<?php

namespace Sanjay\Ragbot\Repositories;

use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Sanjay\Ragbot\Models\Project;

/**
 * Repository for managing Project entities.
 */
class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    /**
     * Create a new project repository instance.
     */
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    /**
     * Find a project by its unique API key.
     */
    public function findByApiKey(string $apiKey): ?Project
    {
        return $this->model
            ->where('api_key', hash('sha256', $apiKey))
            ->where('is_active', true)
            ->first();
    }

    /**
     * Find a project by its slug.
     */
    public function findBySlug(string $slug): ?Project
    {
        return $this->model
            ->where('slug', $slug)
            ->first();
    }
}
