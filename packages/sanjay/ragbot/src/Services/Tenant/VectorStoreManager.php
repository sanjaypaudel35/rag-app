<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Sanjay\Ragbot\Contracts\Services\KeywordSearchInterface;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Enums\VectorStore;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Manager class to dynamically resolve and delegate to the correct vector store driver.
 */
class VectorStoreManager implements KeywordSearchInterface, VectorStoreInterface
{
    /**
     * The registered custom driver creators.
     *
     * @var array<string, \Closure>
     */
    protected array $customCreators = [];

    /**
     * The resolved driver instances.
     *
     * @var array<string, VectorStoreInterface>
     */
    protected array $drivers = [];

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
     * @param  array<string>  $documentIds
     */
    public function search(Project $project, array $queryVector, int $topK = 5, array $documentIds = []): Collection
    {
        return $this->resolve($project)->search($project, $queryVector, $topK, $documentIds);
    }

    /**
     * Searches for chunks matching the query keywords using the project's configured driver.
     *
     * @param  array<string>  $documentIds
     */
    public function searchKeyword(Project $project, string $query, int $topK = 5, array $documentIds = []): Collection
    {
        $driver = $this->resolve($project);

        if ($driver instanceof KeywordSearchInterface) {
            return $driver->searchKeyword($project, $query, $topK, $documentIds);
        }

        return collect();
    }

    /**
     * Resolve the correct vector store driver for the project.
     */
    public function resolve(Project $project): VectorStoreInterface
    {
        /** @var ProjectSetting|null $settings */
        $settings = $project->settings()->withoutGlobalScope('project')->first();

        $driverName = $settings ? $settings->vector_store->value : config('ragbot.vector_store.default');

        if ($driverName === VectorStore::Custom->value && $settings && $settings->vector_store_custom_name) {
            return $this->driver($settings->vector_store_custom_name);
        }

        return $this->driver($driverName);
    }

    /**
     * Get a driver instance by name.
     */
    public function driver(?string $driver = null): VectorStoreInterface
    {
        $driver = $driver ?: config('ragbot.vector_store.default');

        if (! isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }

        return $this->drivers[$driver];
    }

    /**
     * Create a new driver instance.
     */
    protected function createDriver(string $driver): VectorStoreInterface
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        if (class_exists($driver) && is_subclass_of($driver, VectorStoreInterface::class)) {
            return app($driver);
        }

        return match ($driver) {
            VectorStore::PgVector->value => $this->pgVectorStore,
            VectorStore::MySql->value => $this->mysqlVectorStore,
            default => throw new InvalidArgumentException("Driver [{$driver}] not supported or class not found."),
        };
    }

    /**
     * Call a custom driver creator.
     */
    protected function callCustomCreator(string $driver): VectorStoreInterface
    {
        return $this->customCreators[$driver]($this->app ?? app());
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @return $this
     */
    public function extend(string $driver, \Closure $callback): self
    {
        $this->customCreators[$driver] = $callback;

        return $this;
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
