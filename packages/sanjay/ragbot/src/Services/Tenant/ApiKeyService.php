<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Str;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Sanjay\Ragbot\Models\Project;

/**
 * Manages tenant integration API key lifecycle.
 */
class ApiKeyService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected ProjectRepositoryInterface $repository
    ) {}

    /**
     * Regenerates the project's API key.
     */
    public function regenerate(Project $project): string
    {
        $newKey = Str::random(64);

        $this->repository->update($project->id, [
            'api_key' => $newKey,
        ]);

        return $newKey;
    }
}
