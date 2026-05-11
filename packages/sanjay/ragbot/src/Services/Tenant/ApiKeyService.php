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
        $newKey = 'rb_p_'.Str::random(60);

        $this->repository->update($project->id, [
            'api_key' => hash('sha256', $newKey),
        ]);

        return $newKey;
    }
}
