<div>
    <div class="mb-8">
        <flux:heading size="xl">Settings</flux:heading>
        <flux:text class="mt-1">Manage your project configuration, AI providers, and chatbot performance.</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" heading="Success" class="mb-6" closable>
            {{ session('success') }}
        </flux:callout>
    @endif

    <!-- Custom Tab Implementation -->
    <div class="flex gap-2 mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-px">
        <button 
            wire:click="$set('activeTab', 'project')" 
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'project' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            Project Setting
        </button>
        <button 
            wire:click="$set('activeTab', 'llm')" 
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'llm' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            LLM Setting
        </button>
        <button 
            wire:click="$set('activeTab', 'chatbot')" 
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'chatbot' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            Chatbot Setting
        </button>
        <button 
            wire:click="$set('activeTab', 'widget')" 
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'widget' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            Widget Setting
        </button>
    </div>

    <!-- Project Settings Tab -->
    @if($activeTab === 'project')
        <flux:card>
            <form wire:submit.prevent="saveProjectSettings" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <flux:field>
                            <flux:label>Project Name</flux:label>
                            <flux:input wire:model="projectName" />
                            <flux:error name="projectName" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Project Logo</flux:label>
                            <div class="mt-2 flex items-center gap-6">
                                @if($projectLogo)
                                    <img src="{{ $projectLogo->temporaryUrl() }}" class="h-16 w-16 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                                @elseif($currentLogo)
                                    <img src="{{ asset('storage/'.$currentLogo) }}" class="h-16 w-16 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                                @else
                                    <div class="h-16 w-16 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                        <flux:icon icon="photo" class="text-zinc-400 h-6 w-6" />
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <flux:input type="file" wire:model="projectLogo" />
                                    <flux:description>PNG, JPG up to 1MB</flux:description>
                                    <flux:error name="projectLogo" />
                                </div>
                            </div>
                        </flux:field>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-900/50 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800">
                        <flux:heading size="sm" class="mb-2">Project Identity</flux:heading>
                        <flux:text size="sm">These settings define how your project appears to your team and in the public-facing chat widget.</flux:text>
                        
                        <div class="mt-6 space-y-4">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-500">Project Slug</span>
                                <span class="font-mono bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">{{ $project->slug }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-500">Created At</span>
                                <span>{{ $project->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary">Save Changes</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    <!-- LLM Settings Tab -->
    @if($activeTab === 'llm')
        <flux:card>
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- LLM Provider -->
                    <flux:field>
                        <flux:label>LLM Provider</flux:label>
                        <flux:select wire:model.live="settings.llm_provider">
                            @foreach($llmProviders as $provider)
                                <flux:select.option value="{{ $provider->value }}">{{ ucfirst($provider->value) }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="settings.llm_provider" />
                    </flux:field>

                    <!-- LLM API Key -->
                    <flux:field>
                        <flux:label>LLM API Key</flux:label>
                        <flux:input type="password" wire:model="settings.llm_api_key" placeholder="sk-..." />
                        <flux:description>Your provider's API key. Kept secure and encrypted.</flux:description>
                        <flux:error name="settings.llm_api_key" />
                    </flux:field>

                    <!-- LLM Model -->
                    <flux:field>
                        <flux:label>LLM Model</flux:label>
                        <div class="flex gap-2">
                            <flux:select wire:model.live="settings.llm_model" class="flex-1">
                                <flux:select.option value="">Choose a model...</flux:select.option>
                                @foreach($availableModels as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                @endforeach
                                <flux:select.option value="custom">Custom...</flux:select.option>
                            </flux:select>
                            @if($settings['llm_model'] === 'custom' || !array_key_exists($settings['llm_model'], $availableModels))
                                <flux:input wire:model="settings.llm_model" placeholder="Enter custom model" class="flex-1" />
                            @endif
                        </div>
                        <flux:description>The specific model to use for chat operations.</flux:description>
                        <flux:error name="settings.llm_model" />
                    </flux:field>

                    <!-- Embedding Model -->
                    <flux:field>
                        <flux:label>Embedding Model</flux:label>
                        <div class="flex gap-2">
                            <flux:select wire:model.live="settings.llm_model_for_embedding" class="flex-1">
                                <flux:select.option value="">Choose a model...</flux:select.option>
                                @foreach($availableEmbeddingModels as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                @endforeach
                                <flux:select.option value="custom">Custom...</flux:select.option>
                            </flux:select>
                            @if($settings['llm_model_for_embedding'] === 'custom' || !array_key_exists($settings['llm_model_for_embedding'], $availableEmbeddingModels))
                                <flux:input wire:model="settings.llm_model_for_embedding" placeholder="Enter custom model or class" class="flex-1" />
                            @endif
                        </div>
                        <flux:description>The model used for generating vector embeddings. Can be a model name or a custom class.</flux:description>
                        <flux:error name="settings.llm_model_for_embedding" />
                    </flux:field>

                    <!-- LLM API Endpoint -->
                    <flux:field>
                        <flux:label>LLM API Endpoint</flux:label>
                        <flux:input wire:model="settings.llm_api_endpoint" placeholder="https://api.openai.com/v1" />
                        <flux:description>Override the default API endpoint if needed (e.g., for local LLMs).</flux:description>
                        <flux:error name="settings.llm_api_endpoint" />
                    </flux:field>

                    <!-- Embedding API Endpoint -->
                    <flux:field>
                        <flux:label>Embedding API Endpoint</flux:label>
                        <flux:input wire:model="settings.embedding_api_endpoint" placeholder="https://api.openai.com/v1" />
                        <flux:description>Override the default embedding API endpoint if needed.</flux:description>
                        <flux:error name="settings.embedding_api_endpoint" />
                    </flux:field>

                    <!-- Vector Store -->
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Vector Store</flux:label>
                            <flux:select wire:model.live="settings.vector_store" :disabled="isset($project->settings->vector_store)">
                                @foreach($vectorStores as $store)
                                    <flux:select.option value="{{ $store->value }}">{{ ucfirst($store->value) }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            @if(isset($project->settings->vector_store))
                                <flux:description variant="warning" class="flex items-center gap-2 mt-2">
                                    <flux:icon icon="exclamation-triangle" variant="mini" />
                                    Vector store cannot be changed once configured.
                                </flux:description>
                            @else
                                <flux:description>Choose where to store your document embeddings. <strong>Note: This cannot be changed later.</strong></flux:description>
                            @endif
                            <flux:error name="settings.vector_store" />
                        </flux:field>

                        @if($settings['vector_store'] === 'custom')
                            <flux:field>
                                <flux:label>Custom Vector Store Name</flux:label>
                                <flux:input wire:model="settings.vector_store_custom_name" placeholder="e.g. pinecone, weaviate" :disabled="isset($project->settings->vector_store)" />
                                <flux:description>Enter the name of your custom vector store driver.</flux:description>
                                <flux:error name="settings.vector_store_custom_name" />
                            </flux:field>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save LLM Settings</span>
                        <span wire:loading>Saving...</span>
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    <!-- Chatbot Settings Tab -->
    @if($activeTab === 'chatbot')
        <div class="space-y-6">
            @forelse($this->chatbots as $chatbot)
                <flux:card class="overflow-hidden p-0" x-data="{ open: false }">
                    <div 
                        class="bg-zinc-50/50 dark:bg-zinc-900/50 px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between cursor-pointer group"
                        x-on:click="open = !open"
                    >
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105">
                                <flux:icon icon="chat-bubble-left-right" class="text-indigo-600 dark:text-indigo-400 h-5 w-5" />
                            </div>
                            <div>
                                <flux:heading size="md">{{ $chatbot->name }}</flux:heading>
                                <flux:text size="xs" class="font-mono opacity-70">{{ $chatbot->id }}</flux:text>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <flux:button variant="ghost" size="sm" class="pointer-events-none">
                                <span x-show="!open">Configure Settings</span>
                                <span x-show="open">Close Settings</span>
                                <flux:icon icon="chevron-down" variant="mini" class="ml-2 transition-transform duration-300" x-bind:class="open ? 'rotate-180' : ''" />
                            </flux:button>
                            <flux:badge size="sm" variant="neutral" inset="top bottom">Active</flux:badge>
                        </div>
                    </div>

                    <div 
                        x-show="open" 
                        x-collapse 
                        x-cloak
                        class="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/20"
                    >
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Left: Allowed Origins -->
                            <div class="lg:col-span-2">
                                <flux:field>
                                    <flux:label>Allowed Origin URLs</flux:label>
                                    <flux:description>Domains allowed to make cross-origin requests. Use <code class="px-1 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] font-mono">*</code> for all.</flux:description>
                                    <flux:textarea 
                                        wire:model="chatbotSettings.{{ $chatbot->id }}.allowed_origins" 
                                        placeholder="https://example.com" 
                                        rows="4"
                                        class="mt-3 font-mono text-sm"
                                    />
                                    <flux:error name="chatbotSettings.{{ $chatbot->id }}.allowed_origins" />
                                </flux:field>
                            </div>

                            <!-- Right: Rate Limits -->
                            <div class="space-y-6">
                                <flux:field>
                                    <flux:label>Global Rate Limit</flux:label>
                                    <flux:description>Requests per minute total.</flux:description>
                                    <flux:input 
                                        type="number" 
                                        wire:model="chatbotSettings.{{ $chatbot->id }}.rate_limit_per_minute" 
                                        min="1" 
                                        max="1000" 
                                        class="mt-2"
                                    />
                                    <flux:error name="chatbotSettings.{{ $chatbot->id }}.rate_limit_per_minute" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Session Rate Limit</flux:label>
                                    <flux:description>Requests per minute per session.</flux:description>
                                    <flux:input 
                                        type="number" 
                                        wire:model="chatbotSettings.{{ $chatbot->id }}.session_rate_limit_per_minute" 
                                        min="1" 
                                        max="100" 
                                        class="mt-2"
                                    />
                                    <flux:error name="chatbotSettings.{{ $chatbot->id }}.session_rate_limit_per_minute" />
                                </flux:field>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
                            <flux:button 
                                wire:click="saveChatbotSettings('{{ $chatbot->id }}')" 
                                variant="primary" 
                                size="sm"
                                wire:loading.attr="disabled"
                                wire:target="saveChatbotSettings('{{ $chatbot->id }}')"
                            >
                                <span wire:loading.remove wire:target="saveChatbotSettings('{{ $chatbot->id }}')">Update {{ $chatbot->name }}</span>
                                <span wire:loading wire:target="saveChatbotSettings('{{ $chatbot->id }}')">Updating...</span>
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @empty
                <div class="py-20 text-center bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 border-dashed">
                    <flux:icon icon="chat-bubble-bottom-center-text" class="mx-auto h-12 w-12 text-zinc-300" />
                    <flux:heading size="lg" class="mt-4">No chatbots created yet</flux:heading>
                    <flux:text class="mt-2">Create a chatbot in the Chatbot Manager to configure its settings here.</flux:text>
                    <flux:button variant="primary" class="mt-6" href="{{ route('ragbot.settings.chatbots', ['project_slug' => $project->slug]) }}">Go to Chatbot Manager</flux:button>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Widget Settings Tab -->
    @if($activeTab === 'widget')
        <flux:card>
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <!-- Widget Enabled -->
                        <flux:field>
                            <div class="flex items-center justify-between">
                                <div>
                                    <flux:label>Chat Widget Enabled</flux:label>
                                    <flux:description>Allow the chat widget to be displayed on your site.</flux:description>
                                </div>
                                <flux:checkbox.group>
                                    <flux:checkbox wire:model="settings.widget_enabled" />
                                </flux:checkbox.group>
                            </div>
                            <flux:error name="settings.widget_enabled" />
                        </flux:field>

                        <!-- Widget Title -->
                        <flux:field>
                            <flux:label>Widget Title</flux:label>
                            <flux:input wire:model="settings.widget_title" placeholder="Chat with us" />
                            <flux:description>The title displayed in the widget header.</flux:description>
                            <flux:error name="settings.widget_title" />
                        </flux:field>

                        <!-- Widget Color -->
                        <flux:field>
                            <flux:label>Brand Color</flux:label>
                            <div class="flex items-center gap-3">
                                <flux:input type="color" wire:model.live="settings.widget_color" class="w-12 h-10 p-1 rounded-lg" />
                                <flux:input wire:model="settings.widget_color" placeholder="#3b82f6" class="flex-1" />
                            </div>
                            <flux:description>The primary color for your chat widget (button, header, user messages).</flux:description>
                            <flux:error name="settings.widget_color" />
                        </flux:field>

                        <!-- Widget Position -->
                        <flux:field>
                            <flux:label>Widget Position</flux:label>
                            <flux:select wire:model="settings.widget_position">
                                <flux:select.option value="right">Right</flux:select.option>
                                <flux:select.option value="left">Left</flux:select.option>
                            </flux:select>
                            <flux:description>Which side of the screen the widget button should appear.</flux:description>
                            <flux:error name="settings.widget_position" />
                        </flux:field>

                        <!-- Full Page Toggle -->
                        <flux:field>
                            <div class="flex items-center justify-between">
                                <div>
                                    <flux:label>Full Page Mode</flux:label>
                                    <flux:description>Make the chat panel take up more screen space.</flux:description>
                                </div>
                                <flux:checkbox.group>
                                    <flux:checkbox wire:model="settings.widget_full_page" />
                                </flux:checkbox.group>
                            </div>
                            <flux:error name="settings.widget_full_page" />
                        </flux:field>
                    </div>

                    <div class="space-y-6">
                        <!-- Widget Preview (Mockup) -->
                        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 relative overflow-hidden min-h-[300px] flex flex-col items-center justify-center">
                            <flux:heading size="sm" class="absolute top-4 left-4">Live Preview (Mockup)</flux:heading>
                            
                            <!-- Mock Widget Panel -->
                            <div class="w-48 h-64 bg-white dark:bg-zinc-900 rounded-lg shadow-xl border border-zinc-200 dark:border-zinc-800 flex flex-col overflow-hidden transform scale-90">
                                <div class="p-3 text-white font-bold text-[10px] flex justify-between" style="background-color: {{ $settings['widget_color'] }}">
                                    <span>{{ $settings['widget_title'] ?: 'Chat' }}</span>
                                    <span>&times;</span>
                                </div>
                                <div class="flex-1 p-2 space-y-2 bg-zinc-50 dark:bg-zinc-950">
                                    <div class="w-2/3 h-2 bg-zinc-200 dark:bg-zinc-800 rounded"></div>
                                    <div class="w-3/4 h-2 bg-zinc-200 dark:bg-zinc-800 rounded"></div>
                                    <div class="w-1/2 h-4 rounded ml-auto" style="background-color: {{ $settings['widget_color'] }}"></div>
                                </div>
                                <div class="p-2 border-t border-zinc-200 dark:border-zinc-800 flex gap-1">
                                    <div class="flex-1 h-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded"></div>
                                    <div class="w-4 h-4 rounded" style="background-color: {{ $settings['widget_color'] }}"></div>
                                </div>
                            </div>

                            <!-- Mock Widget Button -->
                            <div class="absolute bottom-4 {{ $settings['widget_position'] === 'left' ? 'left-4' : 'right-4' }} w-10 h-10 rounded-full shadow-lg flex items-center justify-center" style="background-color: {{ $settings['widget_color'] }}">
                                <flux:icon icon="chat-bubble-left-right" variant="mini" class="text-white" />
                            </div>
                        </div>

                        <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-lg">
                            <flux:heading size="sm" class="text-indigo-900 dark:text-indigo-300">Widget Integration</flux:heading>
                            <flux:text size="sm" class="mt-1 text-indigo-700 dark:text-indigo-400">After saving your settings, go to the <strong>Integration Guide</strong> to get your embed snippet.</flux:text>
                            <flux:button variant="ghost" size="sm" class="mt-4" href="{{ route('ragbot.integration-guide', ['project_slug' => $project->slug]) }}">View Integration Guide</flux:button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Widget Settings</span>
                        <span wire:loading>Saving...</span>
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endif
</div>
