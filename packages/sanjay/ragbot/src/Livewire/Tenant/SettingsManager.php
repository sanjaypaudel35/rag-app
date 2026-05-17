<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Enums\VectorStore;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Rules\ValidLlmModel;
use Sanjay\Ragbot\Services\Tenant\ProjectSettingsService;

class SettingsManager extends Component
{
    use WithFileUploads;

    public Project $project;

    public array $settings = [];

    public string $activeTab = 'project';

    // Project Name and Logo
    public string $projectName = '';

    public $projectLogo;

    public ?string $currentLogo = null;

    // Properties for inline chatbot settings
    public array $chatbotSettings = [];

    protected function rules(): array
    {
        return [
            'projectName' => ['required', 'string', 'max:255'],
            'projectLogo' => ['nullable', 'image', 'max:1024'], // 1MB Max

            'settings.llm_provider' => ['required', Rule::enum(LlmProvider::class)],
            'settings.llm_api_key' => ['nullable', 'string'],
            'settings.llm_model' => [
                'nullable',
                'string',
                new ValidLlmModel($this->settings['llm_provider'] ?? null),
            ],
            'settings.llm_model_for_embedding' => [
                'nullable',
                'string',
                new ValidLlmModel($this->settings['llm_provider'] ?? null, true),
            ],
            'settings.llm_api_endpoint' => ['nullable', 'url'],
            'settings.embedding_api_endpoint' => ['nullable', 'url'],
            'settings.vector_store' => ['required', Rule::enum(VectorStore::class)],
            'settings.vector_store_custom_name' => ['nullable', 'string', 'required_if:settings.vector_store,custom'],
            'settings.widget_enabled' => ['required', 'boolean'],

            'chatbotSettings.*.allowed_origins' => ['nullable', 'string'],
            'chatbotSettings.*.rate_limit_per_minute' => ['required', 'integer', 'min:1', 'max:1000'],
            'chatbotSettings.*.session_rate_limit_per_minute' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function mount(ProjectSettingsService $service): void
    {
        $this->project = app('ragbot.project');
        $this->settings = $service->getForProject($this->project)->toArray();

        $this->projectName = $this->project->name;
        $this->currentLogo = $this->project->logo;

        // Initialize chatbot settings
        $this->loadChatbotSettings();
    }

    public function saveProjectSettings(): void
    {
        $this->validate([
            'projectName' => 'required|string|max:255',
            'projectLogo' => 'nullable|image|max:1024',
        ]);

        $data = [
            'name' => $this->projectName,
        ];

        if ($this->projectLogo) {
            $path = $this->projectLogo->store('logos', 'public');
            $data['logo'] = $path;
            $this->currentLogo = $path;
        }

        $this->project->update($data);

        session()->flash('success', 'Project settings updated successfully.');
    }

    protected function loadChatbotSettings(): void
    {
        foreach ($this->chatbots as $chatbot) {
            $this->chatbotSettings[$chatbot->id] = [
                'allowed_origins' => implode("\n", $chatbot->allowed_origins ?? []),
                'rate_limit_per_minute' => $chatbot->rate_limit_per_minute ?? 60,
                'session_rate_limit_per_minute' => $chatbot->session_rate_limit_per_minute ?? 10,
            ];
        }
    }

    #[Computed]
    public function chatbots()
    {
        return $this->project->chatbots()->latest()->get();
    }

    public function saveChatbotSettings(string $chatbotId): void
    {
        $this->validate([
            "chatbotSettings.{$chatbotId}.rate_limit_per_minute" => 'required|integer|min:1|max:1000',
            "chatbotSettings.{$chatbotId}.session_rate_limit_per_minute" => 'required|integer|min:1|max:100',
        ]);

        $chatbot = Chatbot::findOrFail($chatbotId);
        $settings = $this->chatbotSettings[$chatbotId];

        $origins = array_filter(array_map('trim', explode("\n", $settings['allowed_origins'])));

        $chatbot->update([
            'allowed_origins' => $origins,
            'rate_limit_per_minute' => $settings['rate_limit_per_minute'],
            'session_rate_limit_per_minute' => $settings['session_rate_limit_per_minute'],
        ]);

        session()->flash('success', "Settings for {$chatbot->name} updated successfully.");
    }

    public function save(ProjectSettingsService $service): void
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $service->update($this->project, $this->settings);

            DB::commit();
            session()->flash('success', 'Settings updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to update settings: '.$e->getMessage());
            session()->flash('error', 'Failed to update settings.');
        }
    }

    public function render(): View
    {
        $provider = LlmProvider::tryFrom($this->settings['llm_provider']) ?? LlmProvider::OpenAI;

        return view('ragbot::livewire.tenant.settings-manager', [
            'llmProviders' => LlmProvider::cases(),
            'vectorStores' => VectorStore::cases(),
            'availableModels' => $provider->models(),
            'availableEmbeddingModels' => $provider->embeddingModels(),
        ])->layout('ragbot::layouts.dashboard');
    }
}
