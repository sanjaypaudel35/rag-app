<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Livewire\Component;
use Sanjay\Ragbot\Enums\LlmModel;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Enums\VectorStore;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Services\Tenant\ProjectSettingsService;

class SettingsManager extends Component
{
    public Project $project;

    public array $settings = [];

    protected $rules = [
        'settings.llm_provider' => 'required|string',
        'settings.llm_api_key' => 'nullable|string',
        'settings.llm_model' => 'nullable|string',
        'settings.llm_model_for_embedding' => 'nullable|string',
        'settings.llm_api_endpoint' => 'nullable|url',
        'settings.embedding_api_endpoint' => 'nullable|url',
        'settings.vector_store' => 'required|string',
        'settings.widget_enabled' => 'required|boolean',
    ];

    public function mount(ProjectSettingsService $service)
    {
        $this->project = app('ragbot.project');
        $this->settings = $service->getForProject($this->project)->toArray();
    }

    public function save(ProjectSettingsService $service)
    {
        $this->validate();

        $service->update($this->project, $this->settings);

        session()->flash('success', 'Settings updated successfully.');
    }

    public function render()
    {
        $provider = LlmProvider::tryFrom($this->settings['llm_provider']) ?? LlmProvider::OpenAI;

        return view('ragbot::livewire.tenant.settings-manager', [
            'llmProviders' => LlmProvider::cases(),
            'vectorStores' => VectorStore::cases(),
            'availableModels' => LlmModel::forProvider($provider),
            'availableEmbeddingModels' => LlmModel::embeddingsForProvider($provider),
        ])->layout('ragbot::layouts.dashboard');
    }
}
