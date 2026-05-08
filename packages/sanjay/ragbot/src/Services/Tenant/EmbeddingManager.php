<?php

namespace Sanjay\Ragbot\Services\Tenant;

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
     * Generate a vector embedding for the given text using the given project's provider.
     *
     * @return array<float>
     */
    public function embed(string $text, ?Project $project = null): array
    {
        $project = $project ?? app('ragbot.project');

        return $this->resolve($project)->embed($text);
    }

    /**
     * Resolve the correct Embedding implementation for the given project.
     */
    public function resolve(Project $project): EmbeddingInterface
    {
        /** @var ProjectSetting|null $settings */
        $settings = $project->settings()->withoutGlobalScope('project')->first();
        $provider = $settings ? $settings->llm_provider : LlmProvider::tryFrom(config('ragbot.embedding.default'));

        return match ($provider) {
            LlmProvider::OpenAI => new OpenAIEmbeddingService($project),
            default => new StubEmbeddingService,
        };
    }
}
