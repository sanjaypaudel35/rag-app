<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <flux:heading size="xl" level="1">Billing & Usage</flux:heading>
            <flux:text class="mt-1">Monitor your chatbot performance and token consumption for <span class="font-medium text-zinc-900 dark:text-white">{{ $project->name }}</span></flux:text>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <flux:input type="date" wire:model.live="startDate" label="From" size="sm" />
            <flux:input type="date" wire:model.live="endDate" label="To" size="sm" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="bolt" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Input Tokens</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">{{ number_format($this->totals['input_tokens']) }}</flux:heading>
                <flux:text size="xs" class="text-zinc-400 font-medium">For the period</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="bolt" variant="mini" class="text-zinc-400" />
                <flux:text size="sm" class="font-medium text-zinc-500 uppercase tracking-wider">Output Tokens</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl">{{ number_format($this->totals['output_tokens']) }}</flux:heading>
                <flux:text size="xs" class="text-zinc-400 font-medium">For the period</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-1 bg-indigo-50/30 dark:bg-indigo-950/10 border-indigo-100 dark:border-indigo-900">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon icon="currency-dollar" variant="mini" class="text-indigo-500" />
                <flux:text size="sm" class="font-medium text-indigo-500 uppercase tracking-wider">Estimated Cost</flux:text>
            </div>
            <div class="flex items-baseline gap-2">
                <flux:heading size="xl" class="text-indigo-600 dark:text-indigo-400">${{ number_format($this->totals['cost'], 4) }}</flux:heading>
                <flux:text size="xs" class="text-indigo-400 font-medium">Based on usage</flux:text>
            </div>
        </flux:card>
    </div>

    <flux:card>
        <div class="flex items-center justify-between mb-6">
            <flux:heading size="lg">Chatbot Performance Breakdown</flux:heading>
        </div>

        @if($this->performanceData->isEmpty())
            <div class="py-12 flex flex-col items-center justify-center text-center">
                <flux:icon icon="chat-bubble-left-right" class="size-12 text-zinc-200 mb-4" />
                <flux:heading size="md">No usage recorded for this period</flux:heading>
                <flux:text class="max-w-xs mx-auto mt-2">Try adjusting the date filters to see usage data.</flux:text>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Chatbot / Model</flux:table.column>
                    <flux:table.column>Input Tokens</flux:table.column>
                    <flux:table.column>Output Tokens</flux:table.column>
                    <flux:table.column>Estimated Cost</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($this->performanceData as $chatbot)
                        <flux:table.row :key="$chatbot['id']" class="{{ $chatbot['deleted_at'] ? 'opacity-50 grayscale bg-red-50/30 dark:bg-red-950/10' : 'bg-zinc-50/50 dark:bg-zinc-800/20' }}">
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:icon icon="chat-bubble-left-right" variant="mini" class="{{ $chatbot['deleted_at'] ? 'text-red-400' : 'text-zinc-400' }}" />
                                    <span class="font-semibold {{ $chatbot['deleted_at'] ? 'text-red-600 dark:text-red-400 line-through' : 'text-zinc-900 dark:text-white' }}">
                                        {{ $chatbot['name'] }}
                                    </span>
                                    @if($chatbot['deleted_at'])
                                        <flux:badge size="sm" variant="danger" inset="top bottom">Deleted</flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">{{ number_format($chatbot['total_input_tokens']) }}</flux:table.cell>
                            <flux:table.cell class="font-medium">{{ number_format($chatbot['total_output_tokens']) }}</flux:table.cell>
                            <flux:table.cell variant="strong" class="text-indigo-600 dark:text-indigo-400">
                                ${{ number_format($chatbot['total_cost'], 4) }}
                            </flux:table.cell>
                        </flux:table.row>

                        @foreach($chatbot['models'] as $model)
                            <flux:table.row :key="$chatbot['id'].$model['model']" class="{{ $chatbot['deleted_at'] ? 'opacity-50 grayscale' : '' }}">
                                <flux:table.cell>
                                    <div class="pl-8 flex items-center gap-2">
                                        <flux:icon icon="cpu-chip" variant="mini" class="{{ $chatbot['deleted_at'] ? 'text-red-300' : 'text-zinc-300' }}" />
                                        <span class="text-sm font-mono {{ $chatbot['deleted_at'] ? 'text-red-600 dark:text-red-400 line-through' : 'text-zinc-600 dark:text-zinc-400' }}">
                                            {{ $model['model'] }}
                                        </span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="text-sm {{ $chatbot['deleted_at'] ? 'text-red-500 line-through' : 'text-zinc-500' }}">{{ number_format($model['input_tokens']) }}</flux:table.cell>
                                <flux:table.cell class="text-sm {{ $chatbot['deleted_at'] ? 'text-red-500 line-through' : 'text-zinc-500' }}">{{ number_format($model['output_tokens']) }}</flux:table.cell>
                                <flux:table.cell class="text-sm {{ $chatbot['deleted_at'] ? 'text-red-500 line-through' : 'text-zinc-500' }}">${{ number_format($model['cost'], 4) }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>

    <div class="mt-8">
        <flux:card variant="subtle">
            <flux:heading size="md" class="mb-2">How cost is calculated</flux:heading>
            <flux:text size="sm">
                Costs are estimated based on the number of tokens processed by each model. 
                Input tokens are the tokens sent to the model (prompt and context), and output tokens are the tokens generated by the model.
                Actual costs may vary slightly depending on the specific LLM provider's billing cycles and any applicable discounts.
            </flux:text>
        </flux:card>
    </div>
</div>
