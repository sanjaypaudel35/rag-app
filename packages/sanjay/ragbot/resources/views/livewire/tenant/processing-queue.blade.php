<div wire:poll.3s>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" level="1">Processing Queue</flux:heading>
            <flux:text class="mt-1">Monitor and manage document processing tasks for <span class="font-medium text-zinc-900 dark:text-white">{{ $project->name }}</span></flux:text>
        </div>
    </div>

    <flux:card>
        @if($this->documents->isEmpty())
            <div class="py-12 flex flex-col items-center justify-center text-center">
                <flux:icon icon="arrow-path" class="size-12 text-zinc-200 mb-4 animate-spin" />
                <flux:heading size="md">Queue is empty</flux:heading>
                <flux:text class="max-w-xs mx-auto mt-2">All documents have been processed successfully. New uploads will appear here during processing.</flux:text>
                <flux:button class="mt-6" variant="primary" href="{{ route('ragbot.documents', ['project_slug' => $project->slug]) }}">Upload Documents</flux:button>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Document</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Progress</flux:table.column>
                    <flux:table.column>Jobs (P/F/T)</flux:table.column>
                    <flux:table.column>Started</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($this->documents as $doc)
                        <flux:table.row :key="$doc->id">
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $doc->name }}</span>
                                    @if($doc->error_message)
                                        <span class="text-xs text-red-500 mt-1 truncate max-w-xs" title="{{ $doc->error_message }}">
                                            Error: {{ $doc->error_message }}
                                        </span>
                                    @endif
                                </div>
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                @php
                                    $hasBatchFailures = $doc->batch && $doc->batch->failedJobs > 0;
                                    $isFailed = $doc->status->value === 'failed' || $hasBatchFailures;
                                @endphp

                                <div class="flex items-center gap-2">
                                    @if($doc->status->value === 'processing' && !$hasBatchFailures)
                                        <flux:badge color="amber" size="sm" inset="top bottom" class="animate-pulse">Processing</flux:badge>
                                    @elseif($isFailed)
                                        <flux:badge color="red" size="sm" inset="top bottom">Failed</flux:badge>
                                        <flux:button variant="ghost" size="xs" icon="information-circle" 
                                            wire:click="viewFailedJobs('{{ $doc->id }}')" 
                                            class="text-red-500 hover:text-red-600 p-0"
                                            title="View Error Logs"
                                        />
                                    @elseif($doc->status->value === 'completed')
                                        <flux:badge color="emerald" size="sm" inset="top bottom">Completed</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm" inset="top bottom">{{ ucfirst($doc->status->value) }}</flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($doc->batch)
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 mb-1 max-w-[100px]">
                                        <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $doc->batch->progress() }}%"></div>
                                    </div>
                                    <flux:text size="xs">{{ $doc->batch->progress() }}% Complete</flux:text>
                                @else
                                    <flux:text size="xs" class="text-zinc-400">Initializing...</flux:text>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($doc->batch)
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs font-medium text-zinc-500" title="Pending">{{ $doc->batch->pendingJobs }}</span>
                                        <span class="text-xs text-zinc-300">/</span>
                                        <span class="text-xs font-medium text-red-500 cursor-pointer hover:underline" title="Failed" wire:click="viewFailedJobs('{{ $doc->id }}')">{{ $doc->batch->failedJobs }}</span>
                                        <span class="text-xs text-zinc-300">/</span>
                                        <span class="text-xs font-medium text-zinc-900 dark:text-white" title="Total">{{ $doc->batch->totalJobs }}</span>
                                    </div>
                                @else
                                    <flux:text size="xs">-</flux:text>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:text size="xs">{{ $doc->created_at->diffForHumans() }}</flux:text>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex justify-end">
                                    <flux:button variant="ghost" size="sm" icon="arrow-path" 
                                        wire:click="retry('{{ $doc->id }}')" 
                                        wire:loading.attr="disabled"
                                        title="Retry Processing"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>

    <flux:modal name="failed-jobs-modal" wire:model="showFailedJobsModal" class="md:min-w-[800px] h-[80vh] p-0 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-white dark:bg-zinc-900">
            <div>
                <flux:heading size="lg">Failed Jobs Log for {{ $selectedDocumentName }} ({{ $failedBatchJobs }} of {{ $totalBatchJobs }} jobs failed)</flux:heading>
                <flux:text size="xs" class="mt-0.5">Log stack and job details for failed processing tasks</flux:text>
            </div>
            <flux:button icon="x-mark" variant="ghost" size="sm" wire:click="$set('showFailedJobsModal', false)" />
        </div>

        <div class="flex-1 overflow-y-auto bg-zinc-50 dark:bg-zinc-950 p-6">
            @if($showFailedJobsModal)
                @if(empty($selectedFailedJobs))
                    <div class="h-full flex flex-col items-center justify-center text-zinc-400">
                        <flux:icon icon="check-circle" class="w-12 h-12 mb-4 opacity-20 text-green-500" />
                        <flux:text>No failed job records found for this document.</flux:text>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($selectedFailedJobs as $job)
                            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden" x-data="{ 
                                copyLog() {
                                    $el.querySelector('.copy-icon').classList.add('hidden');
                                    $el.querySelector('.check-icon').classList.remove('hidden');
                                    navigator.clipboard.writeText($refs.logContent.innerText);
                                    setTimeout(() => {
                                        $el.querySelector('.copy-icon').classList.remove('hidden');
                                        $el.querySelector('.check-icon').classList.add('hidden');
                                    }, 2000);
                                }
                            }">
                                <div class="px-4 py-3 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <flux:badge color="red" size="sm">Failed</flux:badge>
                                        <flux:text size="xs" class="font-mono">{{ $job['uuid'] }}</flux:text>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <flux:text size="xs" class="text-zinc-500">{{ \Carbon\Carbon::parse($job['failed_at'])->diffForHumans() }}</flux:text>
                                        <button @click="copyLog" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors" title="Copy Log to Clipboard">
                                            <flux:icon icon="clipboard" variant="mini" class="copy-icon size-4" />
                                            <flux:icon icon="check" variant="mini" class="check-icon size-4 hidden text-green-500" />
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="p-4 space-y-4">
                                    <div>
                                        <flux:heading size="sm" class="mb-2">Job Payload</flux:heading>
                                        <div class="bg-zinc-900 rounded-lg p-3 overflow-x-auto">
                                            <pre class="text-[10px] text-zinc-300 font-mono">@json($job['payload'], JSON_PRETTY_PRINT)</pre>
                                        </div>
                                    </div>

                                    <div>
                                        <flux:heading size="sm" class="mb-2">Exception Stack Trace</flux:heading>
                                        <div class="bg-red-50 dark:bg-red-950/20 rounded-lg p-3 border border-red-100 dark:border-red-900/30 overflow-x-auto max-h-96">
                                            <pre x-ref="logContent" class="text-[10px] text-red-700 dark:text-red-400 font-mono whitespace-pre-wrap">{{ $job['exception'] }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
        
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end bg-zinc-50 dark:bg-zinc-900/50 gap-3">
            <flux:button variant="ghost" size="sm" wire:click="$set('showFailedJobsModal', false)">Close</flux:button>
            <flux:button variant="primary" size="sm" wire:click="retry('{{ $selectedDocumentId }}')">
                Retry Document
            </flux:button>
        </div>
    </flux:modal>
</div>
