<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" level="1">Overview</flux:heading>
            <flux:text class="mt-1">Insights and management for <span class="font-medium text-zinc-900 dark:text-white">{{ $project->name }}</span></flux:text>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="document-duplicate" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Total Documents</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">{{ $project->documents()->count() }}</flux:heading>
                <flux:text size="xs" class="text-green-600 font-medium">Active</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="chat-bubble-left-right" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Conversations</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">0</flux:heading>
                <flux:text size="xs" class="text-zinc-400 font-medium">Last 30 days</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="cpu-chip" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Storage Used</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">0.0 MB</flux:heading>
                <flux:text size="xs" class="text-zinc-400 font-medium">of 100 MB</flux:text>
            </div>
        </flux:card>
    </div>

    <flux:separator variant="subtle" class="my-10" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <flux:card>
            <flux:heading size="lg" class="mb-4">Getting Started</flux:heading>
            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="flex-none w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-sm font-bold text-zinc-600 dark:text-zinc-400">1</div>
                    <div>
                        <flux:heading size="md">Upload your knowledge base</flux:heading>
                        <flux:text class="mt-1">Add PDF, Word, or Text files to train your assistant on your specific data.</flux:text>
                        <flux:link href="{{ route('ragbot.documents', ['project_slug' => $project->slug]) }}">Go to Documents</flux:link>
                    </div>
                </div>
                <div class="flex gap-4 opacity-50">
                    <div class="flex-none w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-sm font-bold text-zinc-600 dark:text-zinc-400">2</div>
                    <div>
                        <flux:heading size="md">Test your assistant</flux:heading>
                        <flux:text class="mt-1 text-sm">Once processed, start a conversation to see how Ragbot handles your queries.</flux:text>
                    </div>
                </div>
                <div class="flex gap-4 opacity-50">
                    <div class="flex-none w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-sm font-bold text-zinc-600 dark:text-zinc-400">3</div>
                    <div>
                        <flux:heading size="md">Embed the widget</flux:heading>
                        <flux:text class="mt-1 text-sm">Deploy Ragbot to your website or app with a simple copy-paste snippet.</flux:text>
                    </div>
                </div>
            </div>
        </flux:card>

        <div class="space-y-8">
            <flux:card>
                <flux:heading size="md" class="mb-2">Project API Key</flux:heading>
                <flux:text class="mb-4">Use this key to authenticate your requests to the Ragbot API.</flux:text>
                <div class="flex gap-2">
                    <flux:input readonly value="********************************" class="font-mono flex-1" />
                    <flux:button icon="clipboard-document" />
                </div>
            </flux:card>

            <flux:card variant="subtle" class="bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-100 dark:border-indigo-900">
                <flux:heading size="md" class="text-indigo-900 dark:text-indigo-300">Need help?</flux:heading>
                <flux:text class="mt-1 text-indigo-700 dark:text-indigo-400">Our documentation covers everything from basic setup to advanced RAG configurations.</flux:text>
                <flux:link href="#" class="text-indigo-600 dark:text-indigo-400">View Documentation</flux:link>
            </flux:card>
        </div>
    </div>
</div>
