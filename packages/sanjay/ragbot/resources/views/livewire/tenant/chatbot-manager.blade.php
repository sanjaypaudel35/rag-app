<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl">Chatbot Key Management</flux:heading>
            <flux:text class="mt-1">Create and manage your AI chatbots with custom knowledge bases.</flux:text>
        </div>
        <div class="flex gap-3">
            @if ($testingChatbotId)
                <flux:button variant="ghost" icon="x-mark" wire:click="$set('testingChatbotId', null)">Exit Test Chat</flux:button>
            @endif
            <flux:button variant="primary" icon="plus" wire:click="openCreateModal">Create Chatbot</flux:button>
        </div>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" heading="Success" class="mb-6" closable>
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session()->has('info'))
        <flux:callout variant="info" icon="information-circle" heading="Info" class="mb-6" closable>
            {{ session('info') }}
        </flux:callout>
    @endif

    @if ($testingChatbotId)
        <!-- Test Chat Section -->
        <flux:card class="mb-12 border-indigo-200 dark:border-indigo-900 shadow-lg">
            <div class="flex items-center justify-between mb-6 pb-6 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                        <flux:icon icon="chat-bubble-left-right" class="text-indigo-600 dark:text-indigo-400 h-6 w-6" />
                    </div>
                    <div>
                        <flux:heading size="lg">Testing: {{ $this->testingChatbot->name }}</flux:heading>
                        <flux:text size="xs" class="font-mono text-indigo-600 dark:text-indigo-400">using key: {{ $this->maskKey($this->testingChatbot->api_key) }}</flux:text>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Chatbot Switcher -->
                    <flux:dropdown>
                        <flux:button variant="ghost" icon="arrows-right-left" size="sm">Switch Chatbot</flux:button>
                        <flux:menu>
                            @foreach ($this->chatbots as $cb)
                                <flux:menu.item wire:click="startTesting('{{ $cb->id }}')" :disabled="$cb->id === $testingChatbotId">
                                    {{ $cb->name }}
                                </flux:menu.item>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>

                    @if (!$isInitialized)
                        <flux:button variant="primary" icon="play" wire:click="initializeChat">Initialize Chat</flux:button>
                    @endif
                </div>
            </div>

            <div class="min-h-[400px] bg-zinc-50 dark:bg-zinc-900 rounded-xl flex flex-col items-center justify-center p-8 text-center border border-zinc-200 dark:border-zinc-800 border-dashed">
                @if ($isInitialized)
                    <flux:icon icon="cpu-chip" class="h-12 w-12 text-indigo-500 mb-4 animate-pulse" />
                    <flux:heading size="lg">Chatbot Ready</flux:heading>
                    <flux:text class="mt-2 max-w-md">The chatbot has been initialized with the knowledge base from: {{ $this->testingChatbot->documents->pluck('name')->implode(', ') }}.</flux:text>
                    <div class="mt-8 p-4 bg-zinc-200 dark:bg-zinc-800 rounded-lg text-zinc-500 italic">
                        Chatting functionality is coming soon...
                    </div>
                @else
                    <flux:icon icon="rocket-launch" class="h-12 w-12 text-zinc-300 mb-4" />
                    <flux:heading size="lg" class="text-zinc-400">Click initialize to begin</flux:heading>
                    <flux:text class="mt-2">This will prepare the knowledge base and verify the API key.</flux:text>
                    <flux:button variant="primary" class="mt-6" wire:click="initializeChat">Initialize Now</flux:button>
                @endif
            </div>
        </flux:card>
    @endif

    <div class="grid grid-cols-1 gap-6 mb-12">
        @forelse ($this->chatbots as $chatbot)
            <flux:card class="group hover:border-indigo-300 dark:hover:border-indigo-800 transition-colors">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <flux:heading size="lg" class="truncate">{{ $chatbot->name }}</flux:heading>
                            <flux:badge size="sm" variant="neutral" inset="top bottom">ID: {{ substr($chatbot->id, 0, 8) }}</flux:badge>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach ($chatbot->documents as $doc)
                                <flux:badge size="sm" variant="primary" icon="document-text">{{ $doc->name }}</flux:badge>
                            @endforeach
                        </div>

                        <div class="space-y-4">
                            <div>
                                <flux:label class="text-xs uppercase tracking-wider text-zinc-500 font-bold">API Key</flux:label>
                                <div class="mt-1 flex items-center gap-3">
                                    <div class="flex-1 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-1.5 font-mono text-xs">
                                        {{ $this->maskKey($chatbot->api_key) }}
                                    </div>
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="clipboard"
                                        x-on:click="navigator.clipboard.writeText('{{ $chatbot->api_key }}'); alert('Copied to clipboard!')"
                                    />
                                    <flux:button
                                        variant="subtle"
                                        size="sm"
                                        icon="arrow-path"
                                        wire:click="regenerateKey('{{ $chatbot->id }}')"
                                        wire:confirm="Are you sure you want to regenerate the API key for this chatbot?"
                                    >
                                        Regenerate
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 min-w-[220px]">
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 p-2 rounded-lg border border-zinc-200 dark:border-zinc-800 text-center">
                                <flux:text size="xs" class="block text-zinc-500">Tokens</flux:text>
                                <flux:text size="sm" class="font-bold">{{ number_format($chatbot->total_tokens_used) }}</flux:text>
                            </div>
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 p-2 rounded-lg border border-zinc-200 dark:border-zinc-800 text-center">
                                <flux:text size="xs" class="block text-zinc-500">Convs</flux:text>
                                <flux:text size="sm" class="font-bold">{{ number_format($chatbot->total_conversations) }}</flux:text>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <flux:button variant="primary" icon="chat-bubble-left-right" class="flex-1" wire:click="startTesting('{{ $chatbot->id }}')">Test Chat</flux:button>
                            <flux:button variant="danger" icon="trash" wire:click="deleteChatbot('{{ $chatbot->id }}')" wire:confirm="Are you sure you want to delete this chatbot?" />
                        </div>
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="py-20 text-center bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 border-dashed">
                <flux:icon icon="chat-bubble-bottom-center-text" class="mx-auto h-12 w-12 text-zinc-300" />
                <flux:heading size="lg" class="mt-4">No chatbots created yet</flux:heading>
                <flux:text class="mt-2">Get started by creating your first AI chatbot.</flux:text>
                <flux:button variant="primary" class="mt-6" icon="plus" wire:click="openCreateModal">Create Chatbot</flux:button>
            </div>
        @endforelse
    </div>

    <!-- Project-wide API Key Section -->
    <div class="mt-16 pt-12 border-t border-zinc-200 dark:border-zinc-800">
        <div class="mb-6">
            <flux:heading size="lg">Project Master Key</flux:heading>
            <flux:text class="mt-1">This is the main API key for your project. Use it for administrative integrations.</flux:text>
        </div>

        <flux:card>
            <div class="space-y-6">
                <div>
                    <flux:label>Current Master API Key</flux:label>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="flex-1 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg px-4 py-2 font-mono text-sm">
                            {{ $this->maskKey($project->api_key) }}
                        </div>
                        <flux:button
                            variant="primary"
                            icon="arrow-path"
                            wire:click="regenerateProjectKey"
                            wire:confirm="Are you sure you want to regenerate your master API key? All current integrations using the old key will stop working immediately."
                        >
                            Regenerate
                        </flux:button>
                    </div>
                    <flux:description class="mt-2">
                        This key provides full access to your project's data. Keep it highly secure.
                    </flux:description>
                </div>

                @if($newProjectKey)
                    <flux:callout variant="warning" icon="exclamation-triangle" heading="New Master Key Generated" class="mt-6">
                        <p class="mb-2">Please copy your new API key now. You won't be able to see it again!</p>
                        <div class="flex items-center gap-2 p-3 bg-white dark:bg-zinc-800 border border-yellow-200 dark:border-yellow-900/50 rounded-lg font-mono text-sm break-all">
                            {{ $newProjectKey }}
                            <flux:button variant="ghost" size="sm" icon="clipboard" x-on:click="navigator.clipboard.writeText('{{ $newProjectKey }}'); alert('Copied to clipboard!')" />
                        </div>
                    </flux:callout>
                @endif
            </div>
        </flux:card>
    </div>

    <!-- Create Modal -->
    <flux:modal wire:model="showingCreateModal" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Create New Chatbot</flux:heading>
                <flux:text>Configure your chatbot and its knowledge base.</flux:text>
            </div>

            <flux:field>
                <flux:label>Chatbot Name</flux:label>
                <flux:input wire:model="name" placeholder="e.g. Customer Support Bot" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Select Knowledge Base Documents</flux:label>
                <flux:description>These documents will be used by the AI to answer questions.</flux:description>
                
                <div class="mt-4 space-y-2 max-h-60 overflow-y-auto p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800">
                    @forelse ($this->availableDocuments as $doc)
                        <div class="flex items-center gap-3">
                            <flux:checkbox wire:model="selectedDocuments" value="{{ $doc->id }}" label="{{ $doc->name }}" />
                        </div>
                    @empty
                        <flux:text class="italic text-center py-4">No processed documents available. Please upload and process documents first.</flux:text>
                    @endforelse
                </div>
                <flux:error name="selectedDocuments" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" x-on:click="$wire.showingCreateModal = false">Cancel</flux:button>
                <flux:button variant="primary" wire:click="createChatbot">Create Chatbot</flux:button>
            </div>
        </div>
    </flux:modal>

    @if ($newlyGeneratedKey)
        <flux:modal wire:model="newlyGeneratedKey" class="max-w-lg">
            <div class="space-y-6 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                    <flux:icon icon="check" class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                
                <div>
                    <flux:heading size="lg">Chatbot Created Successfully!</flux:heading>
                    <flux:text class="mt-2">Here is your chatbot's API key. Copy it now, as you won't be able to see it again.</flux:text>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl font-mono text-sm break-all flex items-center gap-3">
                    <span class="flex-1">{{ $newlyGeneratedKey }}</span>
                    <flux:button variant="ghost" size="sm" icon="clipboard" x-on:click="navigator.clipboard.writeText('{{ $newlyGeneratedKey }}'); alert('Copied to clipboard!')" />
                </div>

                <flux:button variant="primary" class="w-full" x-on:click="$wire.newlyGeneratedKey = null">Got it, I've saved the key</flux:button>
            </div>
        </flux:modal>
    @endif
</div>
