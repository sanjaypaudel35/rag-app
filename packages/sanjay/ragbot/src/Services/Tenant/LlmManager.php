<?php

namespace Sanjay\Ragbot\Services\Tenant;

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

        return $this->resolve($project)->complete($prompt, $options);
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
