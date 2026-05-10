<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for Project repository.
 */
interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a project by its unique API key.
     */
    public function findByApiKey(string $apiKey): ?Model;

    /**
     * Find a project by its slug.
     */
    public function findBySlug(string $slug): ?Model;
}
