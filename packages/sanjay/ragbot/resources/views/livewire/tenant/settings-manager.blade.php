<div>
    <div class="mb-8">
        <flux:heading size="xl">Project Settings</flux:heading>
        <flux:text class="mt-1">Configure your AI providers, models, and integration settings.</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" heading="Success" class="mb-6" closable>
            {{ session('success') }}
        </flux:callout>
    @endif

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
                            <flux:input wire:model="settings.llm_model_for_embedding" placeholder="Enter custom model" class="flex-1" />
                        @endif
                    </div>
                    <flux:description>The model used for generating vector embeddings.</flux:description>
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
            </div>

            <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Settings</span>
                    <span wire:loading>Saving...</span>
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
