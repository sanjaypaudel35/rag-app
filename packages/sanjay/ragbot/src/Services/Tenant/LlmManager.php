<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Sanjay\Ragbot\Contracts\Services\LlmInterface;
use Sanjay\Ragbot\DTOs\LlmResponse;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Manager class to resolve and delegate to the correct LLM service driver.
 */
class LlmManager implements LlmInterface
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
     * @var array<string, LlmInterface>
     */
    protected array $drivers = [];

    /**
     * Generate a completion for the given prompt using the current project's provider.
     *
     * @param  array<string, mixed>  $options
     */
    public function complete(string $prompt, array $options = []): LlmResponse
    {
        $project = app('ragbot.project');
        $service = $this->resolve($project);
        $provider = class_basename($service);

        Log::info("LLM Request [{$provider}]:", [
            'project_id' => $project->id,
            'prompt' => $prompt,
            'options' => $options,
        ]);

        $response = $service->complete($prompt, $options);

        Log::info("LLM Response [{$provider}]:", [
            'project_id' => $project->id,
            'content' => $response->content,
            'tokens' => [
                'input' => $response->inputTokens,
                'output' => $response->outputTokens,
            ],
        ]);

        return $response;
    }

    /**
     * Resolve the correct LLM implementation for the given project.
     */
    public function resolve(Project $project): LlmInterface
    {
        /** @var ProjectSetting|null $settings */
        $settings = $project->settings()->withoutGlobalScope('project')->first();

        if ($settings?->llm_provider instanceof LlmProvider) {
            $driverName = $settings->llm_provider->value;
        } else {
            $driverName = $settings->llm_provider ?? config('ragbot.llm.default');
        }

        return $this->driver($driverName, $project);
    }

    /**
     * Get a driver instance by name.
     */
    public function driver(?string $driver = null, ?Project $project = null): LlmInterface
    {
        $driver = $driver ?: config('ragbot.llm.default');
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
    protected function createDriver(string $driver, Project $project): LlmInterface
    {
        if (isset($this->customCreators[$driver])) {
            return ($this->customCreators[$driver])(app(), $project);
        }

        if (class_exists($driver) && is_subclass_of($driver, LlmInterface::class)) {
            return app($driver, ['project' => $project]);
        }

        return match ($driver) {
            LlmProvider::OpenAI->value, 'openai' => new OpenAILLMService($project),
            LlmProvider::Anthropic->value, 'anthropic' => new AnthropicLLMService($project),
            LlmProvider::Stub->value, 'stub' => new StubLLMService($project),
            default => throw new InvalidArgumentException("LLM driver [{$driver}] not supported or class not found."),
        };
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
