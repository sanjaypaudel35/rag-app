<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Sanjay\Ragbot\Contracts\Repositories\ProjectSettingRepositoryInterface;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;

/**
 * Manages per-tenant AI and integration configuration.
 */
class ProjectSettingsService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected ProjectSettingRepositoryInterface $repository
    ) {}

    /**
     * Updates project settings.
     */
    public function update(Project $project, array $data): ProjectSetting
    {
        /** @var ProjectSetting|null $existing */
        $existing = $this->repository->findOneBy(['project_id' => $project->id]);

        // If vector_store is already set, don't allow it to be changed.
        if ($existing && $existing->vector_store) {
            unset($data['vector_store']);
        }

        /** @var ProjectSetting $settings */
        $settings = $this->repository->updateOrCreate(
            ['project_id' => $project->id],
            $data
        );

        return $settings;
    }

    /**
     * Get settings for a project.
     */
    public function getForProject(Project $project): ProjectSetting
    {
        /** @var ProjectSetting|null $settings */
        $settings = $this->repository->findOneBy(['project_id' => $project->id]);

        if (! $settings) {
            $settings = $this->repository->create([
                'project_id' => $project->id,
                'llm_provider' => config('ragbot.llm.default'),
                'llm_model' => config('ragbot.llm.providers.'.config('ragbot.llm.default').'.model'),
                'llm_model_for_embedding' => config('ragbot.embedding.providers.'.config('ragbot.embedding.default').'.model'),
                'llm_api_endpoint' => config('ragbot.llm.providers.'.config('ragbot.llm.default').'.base_url'),
                'embedding_api_endpoint' => config('ragbot.embedding.providers.'.config('ragbot.embedding.default').'.base_url'),
                'vector_store' => config('ragbot.vector_store.default'),
                'widget_enabled' => config('ragbot.widget.enabled'),
            ]);
        }

        return $settings;
    }
}
