<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Sanjay\Ragbot\Contracts\Services\LlmInterface;
use Sanjay\Ragbot\Models\Project;

/**
 * Abstract base class for LLM services.
 */
abstract class BaseLlmService implements LlmInterface
{
    /**
     * The project instance.
     */
    protected Project $project;

    /**
     * The project settings.
     */
    protected $settings;

    /**
     * Create a new service instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->settings = $project->settings()->withoutGlobalScope('project')->first();
    }

    /**
     * Build a common prompt structure.
     */
    protected function buildPrompt(string $system, string $user): string
    {
        // Simple default prompt building, can be overridden by providers if needed
        return "System: {$system}\nUser: {$user}\nAssistant:";
    }
}
