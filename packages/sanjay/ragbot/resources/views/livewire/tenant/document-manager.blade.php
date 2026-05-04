<div>
    <!-- Header with Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-zinc-500 mb-1 lg:hidden">
                <flux:text size="sm">Dashboard</flux:text>
                <flux:icon icon="chevron-right" variant="mini" class="w-3 h-3" />
                <flux:text size="sm" class="font-medium text-zinc-900 dark:text-white">Documents</flux:text>
            </div>
            <flux:heading size="xl">Documents</flux:heading>
            <flux:text class="mt-1">Manage and organize your project documents</flux:text>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <flux:card class="relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <div>
                    <flux:text size="xs" class="font-bold uppercase tracking-widest text-zinc-400">Total Documents</flux:text>
                    <flux:heading size="xl" class="mt-2">{{ $documents->count() }}</flux:heading>
                </div>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg group-hover:bg-blue-100 dark:group-hover:bg-blue-900/40 transition-colors">
                    <flux:icon icon="document-text" variant="mini" class="text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <!-- Minimal Chart Line -->
            <div class="mt-6 flex items-end gap-1 h-8">
                <div class="flex-1 bg-blue-100 dark:bg-blue-900/30 rounded-t h-[40%]"></div>
                <div class="flex-1 bg-blue-200 dark:bg-blue-900/40 rounded-t h-[60%]"></div>
                <div class="flex-1 bg-blue-300 dark:bg-blue-900/50 rounded-t h-[30%]"></div>
                <div class="flex-1 bg-blue-400 dark:bg-blue-900/60 rounded-t h-[80%]"></div>
                <div class="flex-1 bg-blue-500 dark:bg-blue-500 rounded-t h-[50%]"></div>
            </div>
        </flux:card>

        <flux:card class="group">
            <div class="flex justify-between items-start">
                <div>
                    <flux:text size="xs" class="font-bold uppercase tracking-widest text-zinc-400">Processing</flux:text>
                    <flux:heading size="xl" class="mt-2">{{ $documents->where('status', \Sanjay\Ragbot\Enums\DocumentStatus::Processing)->count() }}</flux:heading>
                </div>
                <div class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg group-hover:bg-yellow-100 dark:group-hover:bg-yellow-900/40 transition-colors">
                    <flux:icon icon="arrow-path" variant="mini" class="text-yellow-600 dark:text-yellow-400" />
                </div>
            </div>
            <div class="mt-6 flex items-center gap-2">
                <div class="flex-1 h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-500 animate-pulse w-1/3"></div>
                </div>
                <flux:text size="xs" class="text-yellow-600 font-medium">In Queue</flux:text>
            </div>
        </flux:card>

        <flux:card class="group">
            <div class="flex justify-between items-start">
                <div>
                    <flux:text size="xs" class="font-bold uppercase tracking-widest text-zinc-400">Completed</flux:text>
                    <flux:heading size="xl" class="mt-2">{{ $documents->where('status', \Sanjay\Ragbot\Enums\DocumentStatus::Completed)->count() }}</flux:heading>
                </div>
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg group-hover:bg-green-100 dark:group-hover:bg-green-900/40 transition-colors">
                    <flux:icon icon="check-circle" variant="mini" class="text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="mt-6 flex items-center gap-2">
                <div class="flex-1 h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 w-full"></div>
                </div>
                <flux:text size="xs" class="text-green-600 font-medium">Healthy</flux:text>
            </div>
        </flux:card>

        <flux:card class="group">
            <div class="flex justify-between items-start">
                <div>
                    <flux:text size="xs" class="font-bold uppercase tracking-widest text-zinc-400">Failed</flux:text>
                    <flux:heading size="xl" class="mt-2">{{ $documents->where('status', \Sanjay\Ragbot\Enums\DocumentStatus::Failed)->count() }}</flux:heading>
                </div>
                <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg group-hover:bg-red-100 dark:group-hover:bg-red-900/40 transition-colors">
                    <flux:icon icon="x-circle" variant="mini" class="text-red-600 dark:text-red-400" />
                </div>
            </div>
            <div class="mt-6 flex items-center gap-2">
                <div class="flex-1 h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-red-500 w-[5%]"></div>
                </div>
                <flux:text size="xs" class="text-red-600 font-medium">Issues</flux:text>
            </div>
        </flux:card>
    </div>

    <!-- Feedback Messages -->
    @if ($successMessage)
        <flux:callout variant="success" icon="check-circle" heading="Success" class="mb-6" closable wire:click="clearMessages">
            {{ $successMessage }}
        </flux:callout>
    @endif

    @if ($errorMessage)
        <flux:callout variant="danger" icon="exclamation-circle" heading="Error" class="mb-6" closable wire:click="clearMessages">
            {{ $errorMessage }}
        </flux:callout>
    @endif

    <!-- Main Table Card -->
    <flux:card class="p-0 overflow-hidden shadow-sm border-zinc-200 dark:border-zinc-800">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-zinc-900">
            <flux:heading size="md">All Documents</flux:heading>
            
            <div class="flex items-center gap-3">
                <flux:input icon="magnifying-glass" placeholder="Search..." size="sm" class="w-full lg:w-44" />
                <flux:select size="sm" placeholder="Filter" class="w-full lg:w-32">
                    <flux:select.option>All Status</flux:select.option>
                    <flux:select.option>Completed</flux:select.option>
                    <flux:select.option>Processing</flux:select.option>
                    <flux:select.option>Failed</flux:select.option>
                </flux:select>
                <flux:button icon="plus" variant="primary" size="sm" wire:click="$set('showUploadModal', true)">Upload</flux:button>
            </div>
        </div>

        @if ($documents->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 px-6 text-center">
                <div class="w-20 h-20 rounded-2xl bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center mb-6">
                    <flux:icon icon="document-plus" class="w-10 h-10 text-zinc-300" />
                </div>
                <flux:heading size="lg">No documents found</flux:heading>
                <flux:text class="mt-2 max-w-xs mx-auto">Get started by uploading your first document to your knowledge base.</flux:text>
                <flux:button variant="primary" class="mt-8" wire:click="$set('showUploadModal', true)">Upload your first file</flux:button>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>File Name</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Uploaded On</flux:table.column>
                    <flux:table.column>File Size</flux:table.column>
                    <flux:table.column>Uploaded By</flux:table.column>
                    <flux:table.column align="end"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($documents as $document)
                        <flux:table.row :key="$document->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg group-hover:bg-white dark:group-hover:bg-zinc-700 transition-colors">
                                        <flux:icon icon="document-text" variant="mini" class="text-zinc-400" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-zinc-900 dark:text-white truncate max-w-[240px]" title="{{ $document->name }}">
                                            {{ $document->name }}
                                        </span>
                                        <span class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold">{{ strtoupper($document->mime_type) }}</span>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $status = match($document->status->value) {
                                        "completed" => ["color" => "green", "label" => "Completed"],
                                        "failed" => ["color" => "red", "label" => "Failed"],
                                        "processing" => ["color" => "yellow", "label" => "Processing"],
                                        default => ["color" => "zinc", "label" => "Pending"],
                                    };
                                @endphp
                                <flux:badge :color="$status['color']" size="sm" class="px-2">{{ $status['label'] }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-500 text-sm">
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $document->created_at->format('M d, Y') }}</span>
                                    <span class="text-[10px] text-zinc-400 uppercase tracking-tighter">{{ $document->created_at->format('H:i A') }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-500 text-sm italic">
                                {{ $document->display_size ?? '---' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:avatar size="xs" initials="{{ strtoupper(substr(Auth::guard('ragbot')->user()->name ?? 'S', 0, 1)) }}" class="bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400" />
                                    <flux:text size="sm" class="font-medium">{{ explode(' ', Auth::guard('ragbot')->user()->name ?? 'User')[0] }}</flux:text>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:dropdown align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye" wire:click="preview('{{ $document->id }}')">Preview Document</flux:menu.item>
                                        <flux:menu.item icon="arrow-down-tray" href="{{ route('ragbot.documents.preview', ['project_slug' => $project->slug, 'document' => $document->id]) }}" download>Download</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item 
                                            variant="danger" 
                                            icon="trash" 
                                            wire:click="delete('{{ $document->id }}')" 
                                            wire:confirm="Permanently delete this document?"
                                        >
                                            Delete
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-900/30">
                <flux:text size="xs" class="text-zinc-500">Showing 1 to {{ $documents->count() }} of {{ $documents->count() }} documents</flux:text>
                <div class="flex gap-2">
                    <flux:button size="sm" variant="ghost" class="text-zinc-400 cursor-not-allowed">Previous</flux:button>
                    <flux:button size="sm" variant="ghost" class="text-zinc-400 cursor-not-allowed">Next</flux:button>
                </div>
            </div>
        @endif
    </flux:card>

    <!-- Document Preview Modal -->
    <flux:modal name="preview-modal" wire:model="showPreviewModal" class="md:min-w-[800px] h-[80vh] p-0 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-white dark:bg-zinc-900">
            <div>
                <flux:heading size="lg">{{ $previewName }}</flux:heading>
                <flux:text size="xs" class="mt-0.5">Document Preview</flux:text>
            </div>
            <flux:modal.close>
                <flux:button icon="x-mark" variant="ghost" size="sm" />
            </flux:modal.close>
        </div>

        <div class="flex-1 bg-zinc-100 dark:bg-zinc-950 p-4">
            @if($previewUrl)
                <iframe src="{{ $previewUrl }}" class="w-full h-full rounded-lg border border-zinc-200 dark:border-zinc-800 shadow-inner bg-white" frameborder="0"></iframe>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-zinc-400">
                    <flux:icon icon="document-text" class="w-12 h-12 mb-4 opacity-20" />
                    <flux:text>No document selected for preview</flux:text>
                </div>
            @endif
        </div>
        
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end bg-zinc-50 dark:bg-zinc-900/50">
            <flux:modal.close>
                <flux:button variant="primary" size="sm">Close Preview</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <!-- Upload Modal (Refined) -->
    <flux:modal name="upload-modal" wire:model="showUploadModal" class="md:min-w-[480px] p-0 overflow-hidden">
        <div class="bg-white dark:bg-zinc-900">
            <div class="px-8 py-6 border-b border-zinc-100 dark:border-zinc-800">
                <flux:heading size="lg">Upload Document</flux:heading>
                <flux:text class="mt-1">Add a new file to your project's knowledge base.</flux:text>
            </div>

            <form wire:submit.prevent="handleUpload" class="px-8 py-8 space-y-8">
                <flux:field>
                    <flux:label>Source File</flux:label>
                    <flux:input type="file" name="selectedFile" wire:model.live="selectedFile" class="file:bg-zinc-100 dark:file:bg-zinc-800 file:border-0 file:rounded-md file:px-3 file:py-1 file:text-xs file:font-semibold" />
                    <flux:description>Support for PDF, DOCX, and TXT up to 10MB.</flux:description>
                    <flux:error name="selectedFile" />
                </flux:field>

                <div class="flex gap-3 justify-end pt-4">
                    <flux:button variant="ghost" wire:click="$set('showUploadModal', false)">Cancel</flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="handleUpload" class="px-8">
                        <span wire:loading.remove wire:target="handleUpload">Upload & Process</span>
                        <span wire:loading wire:target="handleUpload">Processing...</span>
                    </flux:button>
                </div>
                
                <div wire:loading wire:target="selectedFile" class="flex items-center gap-2 text-xs text-indigo-600 justify-center font-medium bg-indigo-50 dark:bg-indigo-900/20 py-2 rounded-lg">
                    <flux:icon icon="arrow-path" class="animate-spin w-3 h-3" />
                    Securely preparing file for transmission...
                </div>
            </form>
        </div>
    </flux:modal>
</div>
