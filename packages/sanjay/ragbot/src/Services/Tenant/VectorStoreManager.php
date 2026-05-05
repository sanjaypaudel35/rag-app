<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Enums\VectorStore;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Manager class to dynamically resolve and delegate to the correct vector store driver.
 */
class VectorStoreManager implements VectorStoreInterface
{
    /**
     * Create a new manager instance.
     */
    public function __construct(
        protected PgVectorStoreService $pgVectorStore,
        protected MysqlVectorStoreService $mysqlVectorStore
    ) {}

    /**
     * Stores vector embedding for a chunk using the project's configured driver.
     *
     * @param  array<float>  $vector
     */
    public function store(Project $project, string $chunkId, array $vector): void
    {
        $this->resolve($project)->store($project, $chunkId, $vector);
    }

    /**
     * Searches for chunks similar to the query vector using the project's configured driver.
     *
     * @param  array<float>  $queryVector
     */
    public function search(Project $project, array $queryVector, int $topK = 5): Collection
    {
        return $this->resolve($project)->search($project, $queryVector, $topK);
    }

    /**
     * Resolve the correct vector store driver for the project.
     */
    public function resolve(Project $project): VectorStoreInterface
    {
        /** @var ProjectSetting|null $settings */
        $settings = $project->settings()->withoutGlobalScope('project')->first();
        $driverValue = $settings ? $settings->vector_store->value : config('ragbot.vector_store.default');

        return match ($driverValue) {
            VectorStore::PgVector->value => $this->pgVectorStore,
            VectorStore::MySql->value => $this->mysqlVectorStore,
            default => $this->pgVectorStore,
        };
    }

    /**
     * Get the appropriate vector store driver for the project.
     *
     * @deprecated Use resolve() instead.
     */
    protected function getDriver(Project $project): VectorStoreInterface
    {
        return $this->resolve($project);
    }
}
