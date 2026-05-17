<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Services\Tenant\ApiKeyService;
use Sanjay\Ragbot\Services\Tenant\ChatService;

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

    public string $chatInput = '';

    public array $messages = [];

    public function mount(): void
    {
        $this->project = app('ragbot.project');
    }

    public function startTesting(string $chatbotId): void
    {
        $this->testingChatbotId = $chatbotId;
        $this->isInitialized = false;
        $this->messages = [];
        $this->chatInput = '';
    }

    public function initializeChat(): void
    {
        $this->isInitialized = true;

        $chatbot = $this->testingChatbot;
        if ($chatbot) {
            $conversation = $chatbot->conversations()
                ->where('session_id', 'test-session-'.$chatbot->id)
                ->first();

            if ($conversation) {
                $this->messages = $conversation->messages()
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(fn ($m) => [
                        'role' => $m->role->value,
                        'content' => $m->content,
                    ])
                    ->toArray();
            }
        }

        session()->flash('info', 'Chat initialized with Chatbot API Key.');
    }

    public function sendMessage(ChatService $chatService): void
    {
        $this->validate([
            'chatInput' => 'required|string|min:1',
        ]);

        $chatbot = $this->testingChatbot;
        if (! $chatbot) {
            return;
        }

        $userMessage = $this->chatInput;
        $this->chatInput = '';

        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        try {
            $sessionId = 'test-session-'.$chatbot->id;

            // Bind the chatbot to the container so RetrievalService can use its scope
            app()->instance('ragbot.chatbot', $chatbot);

            $response = $chatService->chat($this->project, $sessionId, $userMessage);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response,
            ];
        } catch (\Throwable $e) {
            Log::error('Chat failed: '.$e->getMessage());
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Sorry, I encountered an error: '.$e->getMessage(),
            ];
        }
    }

    #[Computed]
    public function testingChatbot(): ?Chatbot
    {
        return $this->testingChatbotId ? Chatbot::find($this->testingChatbotId) : null;
    }

    public function regenerateProjectKey(ApiKeyService $service): void
    {
        DB::beginTransaction();

        try {
            $this->newProjectKey = $service->regenerate($this->project);
            $this->project->refresh();

            DB::commit();
            session()->flash('success', 'Project API key regenerated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to regenerate project key: '.$e->getMessage());
            session()->flash('error', 'Failed to regenerate project key.');
        }
    }

    #[Computed]
    public function chatbots(): Collection
    {
        return $this->project->chatbots()->with(['documents', 'modelUsage'])->latest()->get();
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

        DB::beginTransaction();

        try {
            $apiKey = 'rb_c_'.Str::random(60);

            /** @var Chatbot $chatbot */
            $chatbot = $this->project->chatbots()->create([
                'name' => $this->name,
                'api_key' => hash('sha256', $apiKey),
            ]);

            $chatbot->documents()->sync($this->selectedDocuments);

            $this->newlyGeneratedKey = $apiKey;

            DB::commit();
            session()->flash('success', 'Chatbot created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create chatbot: '.$e->getMessage());
            session()->flash('error', 'Failed to create chatbot.');
        }

        $this->showingCreateModal = false;
    }

    public function regenerateKey(Chatbot $chatbot): void
    {
        DB::beginTransaction();

        try {
            $newKey = 'rb_c_'.Str::random(60);
            $chatbot->update(['api_key' => hash('sha256', $newKey)]);

            $this->newlyGeneratedKey = $newKey;

            DB::commit();
            session()->flash('success', "API key for {$chatbot->name} regenerated.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to regenerate chatbot key: '.$e->getMessage());
            session()->flash('error', 'Failed to regenerate API key.');
        }
    }

    public function deleteChatbot(Chatbot $chatbot): void
    {
        DB::beginTransaction();

        try {
            $chatbot->delete();

            DB::commit();
            session()->flash('success', 'Chatbot deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to delete chatbot: '.$e->getMessage());
            session()->flash('error', 'Failed to delete chatbot.');
        }
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
