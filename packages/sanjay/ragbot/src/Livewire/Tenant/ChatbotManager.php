<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Services\Tenant\ApiKeyService;

class ChatbotManager extends Component
{
    public Project $project;

    public string $name = '';

    public array $selectedDocuments = [];

    public ?string $newlyGeneratedKey = null;

    public bool $showingCreateModal = false;

    public ?string $newProjectKey = null;

    public ?string $testingChatbotId = null;

    public bool $isInitialized = false;

    public function mount(): void
    {
        $this->project = app('ragbot.project');
    }

    public function startTesting(string $chatbotId): void
    {
        $this->testingChatbotId = $chatbotId;
        $this->isInitialized = false;
    }

    public function initializeChat(): void
    {
        $this->isInitialized = true;
        // Logic for initializing chat with the selected chatbot's API key would go here.
        session()->flash('info', 'Chat initialized with Chatbot API Key.');
    }

    #[Computed]
    public function testingChatbot(): ?Chatbot
    {
        return $this->testingChatbotId ? Chatbot::find($this->testingChatbotId) : null;
    }

    public function regenerateProjectKey(ApiKeyService $service): void
    {
        $this->newProjectKey = $service->regenerate($this->project);
        $this->project->refresh();

        session()->flash('success', 'Project API key regenerated successfully.');
    }

    #[Computed]
    public function chatbots(): Collection
    {
        return $this->project->chatbots()->with('documents')->latest()->get();
    }

    #[Computed]
    public function availableDocuments(): Collection
    {
        return $this->project->documents()->where('status', DocumentStatus::Completed)->get();
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'selectedDocuments', 'newlyGeneratedKey']);
        $this->showingCreateModal = true;
    }

    public function createChatbot(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'selectedDocuments' => 'required|array|min:1',
        ]);

        $apiKey = 'rb_'.Str::random(40);

        /** @var Chatbot $chatbot */
        $chatbot = $this->project->chatbots()->create([
            'name' => $this->name,
            'api_key' => $apiKey,
        ]);

        $chatbot->documents()->sync($this->selectedDocuments);

        $this->newlyGeneratedKey = $apiKey;
        $this->showingCreateModal = false;

        session()->flash('success', 'Chatbot created successfully.');
    }

    public function regenerateKey(Chatbot $chatbot): void
    {
        $newKey = 'rb_'.Str::random(40);
        $chatbot->update(['api_key' => $newKey]);

        session()->flash('success', "API key for {$chatbot->name} regenerated.");
    }

    public function deleteChatbot(Chatbot $chatbot): void
    {
        $chatbot->delete();
        session()->flash('success', 'Chatbot deleted successfully.');
    }

    public function maskKey(string $key): string
    {
        if (strlen($key) <= 12) {
            return '********';
        }

        return substr($key, 0, 8).str_repeat('*', strlen($key) - 12).substr($key, -4);
    }

    public function render(): View
    {
        return view('ragbot::livewire.tenant.chatbot-manager')
            ->layout('ragbot::layouts.dashboard');
    }
}
