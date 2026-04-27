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
     *
     * @param string $apiKey
     * @return Model|null
     */
    public function findByApiKey(string $apiKey): ?Model;
}
