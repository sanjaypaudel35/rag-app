<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" level="1">Overview</flux:heading>
            <flux:text class="mt-1">Insights and management for <span class="font-medium text-zinc-900 dark:text-white">{{ $project->name }}</span></flux:text>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="document-duplicate" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Total Documents</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">{{ $project->documents()->count() }}</flux:heading>
                <flux:badge size="sm" color="green" inset="top bottom">Active</flux:badge>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="chat-bubble-left-right" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Total Chatbots</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">{{ $this->chatbots->count() }}</flux:heading>
                <flux:text size="xs" class="text-zinc-400 font-medium">Configured</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="bolt" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Tokens Used</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">{{ Number::abbreviate($settings->total_tokens_used) }}</flux:heading>
                <flux:text size="xs" class="text-zinc-400 font-medium">Lifetime</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="currency-dollar" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Estimated Cost</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">{{ Number::currency($this->totalCost) }}</flux:heading>
                <flux:text size="xs" class="text-zinc-400 font-medium">Avg. Rate</flux:text>
            </div>
        </flux:card>
    </div>

    <flux:separator variant="subtle" class="my-10" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <flux:card>
                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="lg">Chatbot Performance</flux:heading>
                    <flux:button variant="ghost" size="sm" href="{{ route('ragbot.settings.chatbots', ['project_slug' => $project->slug]) }}">Manage Chatbots</flux:button>
                </div>

                @if($this->chatbots->isEmpty())
                    <div class="py-12 flex flex-col items-center justify-center text-center">
                        <flux:icon icon="chat-bubble-left-right" class="size-12 text-zinc-200 mb-4" />
                        <flux:heading size="md">No chatbots created yet</flux:heading>
                        <flux:text class="max-w-xs mx-auto mt-2">Start by creating a chatbot to interact with your uploaded documents.</flux:text>
                        <flux:button class="mt-6" href="{{ route('ragbot.settings.chatbots', ['project_slug' => $project->slug]) }}">Create First Chatbot</flux:button>
                    </div>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Name</flux:table.column>
                            <flux:table.column>Tokens Used</flux:table.column>
                            <flux:table.column>Conversations</flux:table.column>
                            <flux:table.column>Est. Cost</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach($this->chatbots as $chatbot)
                                <flux:table.row :key="$chatbot->id">
                                    <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $chatbot->name }}</flux:table.cell>
                                    <flux:table.cell>{{ number_format($chatbot->total_tokens_used) }}</flux:table.cell>
                                    <flux:table.cell>{{ number_format($chatbot->total_conversations) }}</flux:table.cell>
                                    <flux:table.cell variant="strong">{{ Number::currency(($chatbot->total_tokens_used / 1000000) * 0.50) }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @endif
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-6">Project API Key</flux:heading>
                <flux:text class="mb-4">Use this key to authenticate your requests to the Ragbot API from your backend applications.</flux:text>
                <div class="flex gap-2">
                    <flux:input readonly value="{{ $project->api_key }}" class="font-mono flex-1" />
                    <flux:button icon="clipboard-document" x-on:click="navigator.clipboard.writeText('{{ $project->api_key }}')" />
                </div>
            </flux:card>
        </div>

        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-6">AI Configuration</flux:heading>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white dark:bg-zinc-800 rounded-md shadow-sm">
                                <flux:icon icon="cpu-chip" variant="mini" class="text-indigo-500" />
                            </div>
                            <div>
                                <flux:text size="sm" class="font-medium">LLM Provider</flux:text>
                                <flux:text size="xs" class="text-zinc-500">{{ ucfirst($settings->llm_provider->value) }}</flux:text>
                            </div>
                        </div>
                        <flux:badge size="sm" color="indigo" inset="top bottom">{{ $settings->llm_model ?? 'Not Set' }}</flux:badge>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white dark:bg-zinc-800 rounded-md shadow-sm">
                                <flux:icon icon="variable" variant="mini" class="text-emerald-500" />
                            </div>
                            <div>
                                <flux:text size="sm" class="font-medium">Embedding Model</flux:text>
                                <flux:text size="xs" class="text-zinc-500">Vector generation</flux:text>
                            </div>
                        </div>
                        <flux:text size="xs" class="font-mono bg-zinc-100 dark:bg-zinc-900 px-2 py-1 rounded">{{ $settings->llm_model_for_embedding ?? 'Default' }}</flux:text>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white dark:bg-zinc-800 rounded-md shadow-sm">
                                <flux:icon icon="server" variant="mini" class="text-blue-500" />
                            </div>
                            <div>
                                <flux:text size="sm" class="font-medium">Vector Database</flux:text>
                                <flux:text size="xs" class="text-zinc-500">Storage driver</flux:text>
                            </div>
                        </div>
                        <flux:badge size="sm" color="blue" inset="top bottom">{{ ucfirst($settings->vector_store->value) }}</flux:badge>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:button variant="ghost" size="sm" icon="cog-6-tooth" href="{{ route('ragbot.settings', ['project_slug' => $project->slug]) }}">Adjust Settings</flux:button>
                </div>
            </flux:card>

            <flux:card variant="subtle" class="bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-100 dark:border-indigo-900">
                <flux:heading size="md" class="text-indigo-900 dark:text-indigo-300">Need help?</flux:heading>
                <flux:text class="mt-1 text-indigo-700 dark:text-indigo-400 text-sm">Our documentation covers everything from basic setup to advanced RAG configurations.</flux:text>
                <flux:link href="#" class="text-indigo-600 dark:text-indigo-400 text-sm mt-3 block">View Documentation</flux:link>
            </flux:card>
        </div>
    </div>
</div>
