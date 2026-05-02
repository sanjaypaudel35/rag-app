<div>
    <flux:heading size="xl" level="1">Dashboard</flux:heading>
    <flux:text class="mb-6 mt-2 text-base">Overview for {{ $project->name }}</flux:text>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Project Name</flux:text>
            <flux:heading size="lg" class="mt-1">{{ $project->name }}</flux:heading>
        </flux:card>

        <flux:card>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Documents</flux:text>
            <flux:heading size="lg" class="mt-1">{{ $project->documents()->count() }}</flux:heading>
        </flux:card>

        <flux:card>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Messages</flux:text>
            <flux:heading size="lg" class="mt-1">0</flux:heading>
        </flux:card>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <div class="mt-8">
        <flux:card>
            <flux:heading size="md">Welcome to your Ragbot Dashboard</flux:heading>
            <flux:text class="mt-2">This is where you can manage your AI project, upload documents, and configure settings.</flux:text>
        </flux:card>
    </div>
</div>
