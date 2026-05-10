<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Facades\Log;
use Sanjay\Ragbot\Contracts\Services\LlmInterface;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Manager class to resolve and delegate to the correct LLM service driver.
 */
class LlmManager implements LlmInterface
{
    /**
     * Generate a completion for the given prompt using the current project's provider.
     *
     * @param  array<string, mixed>  $options
     */
    public function complete(string $prompt, array $options = []): string
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
            'response' => $response,
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
        $provider = $settings ? $settings->llm_provider : LlmProvider::tryFrom(config('ragbot.llm.default'));

        return match ($provider) {
            LlmProvider::OpenAI => new OpenAILLMService($project),
            LlmProvider::Anthropic => new AnthropicLLMService($project),
            default => new StubLLMService($project),
        };
    }
}
