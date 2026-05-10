<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Manager class to resolve and delegate to the correct Embedding service driver.
 */
class EmbeddingManager implements EmbeddingInterface
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
     * @var array<string, EmbeddingInterface>
     */
    protected array $drivers = [];

    /**
     * Generate a vector embedding for the given text using the given project's provider.
     *
     * @return array<float>
     */
    public function embed(string $text, ?Project $project = null): array
    {
        $project = $project ?? app('ragbot.project');
        $service = $this->resolve($project);
        $provider = class_basename($service);

        Log::info("Embedding Request [{$provider}]:", [
            'project_id' => $project->id,
            'text_preview' => Str::limit($text, 100),
        ]);

        $vector = $service->embed($text);

        Log::info("Embedding Response [{$provider}]:", [
            'project_id' => $project->id,
            'vector_dimensions' => count($vector),
        ]);

        return $vector;
    }

    /**
     * Resolve the correct Embedding implementation for the given project.
     */
    public function resolve(Project $project): EmbeddingInterface
    {
        /** @var ProjectSetting|null $settings */
        $settings = $project->settings()->withoutGlobalScope('project')->first();

        $modelOrClass = $settings?->llm_model_for_embedding;

        // 1. Check if the model name is a registered custom slug or a class
        if ($modelOrClass) {
            if (isset($this->customCreators[$modelOrClass])) {
                return $this->driver($modelOrClass, $project);
            }

            if (class_exists($modelOrClass) && is_subclass_of($modelOrClass, EmbeddingInterface::class)) {
                return $this->driver($modelOrClass, $project);
            }
        }

        // 2. Fall back to the LLM provider's default embedding service
        $provider = $settings ? $settings->llm_provider : LlmProvider::tryFrom(config('ragbot.embedding.default'));

        return $this->driver($provider->value, $project);
    }

    /**
     * Get a driver instance by name.
     */
    public function driver(?string $driver = null, ?Project $project = null): EmbeddingInterface
    {
        $driver = $driver ?: config('ragbot.embedding.default');
        $project = $project ?? app('ragbot.project');

        $instanceKey = $driver.':'.($project?->id ?? 'default');

        if (! isset($this->drivers[$instanceKey])) {
            $this->drivers[$instanceKey] = $this->createDriver($driver, $project);
        }

        return $this->drivers[$instanceKey];
    }

    /**
     * Create a new driver instance.
     */
    protected function createDriver(string $driver, Project $project): EmbeddingInterface
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver, $project);
        }

        if (class_exists($driver) && is_subclass_of($driver, EmbeddingInterface::class)) {
            return app($driver, ['project' => $project]);
        }

        return match ($driver) {
            LlmProvider::OpenAI->value, 'openai' => new OpenAIEmbeddingService($project),
            LlmProvider::Stub->value, 'stub' => new StubEmbeddingService,
            default => throw new InvalidArgumentException("Embedding driver [{$driver}] not supported or class not found."),
        };
    }

    /**
     * Call a custom driver creator.
     */
    protected function callCustomCreator(string $driver, Project $project): EmbeddingInterface
    {
        return $this->customCreators[$driver](app(), $project);
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
}
