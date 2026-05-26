<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Exceptions\DocumentProcessingException;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Services\Tenant\DocumentService;

/**
 * Livewire component for managing project documents.
 */
class DocumentManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    /**
     * Manual document title.
     */
    public $manualTitle = '';

    /**
     * Manual document content.
     */
    public $manualContent = '';

    /**
     * Current active tab in upload modal.
     */
    public $activeTab = 'upload';

    /**
     * The selected file for upload.
     */
    public $selectedFile;

    /**
     * The success message.
     */
    public $successMessage;

    /**
     * The error message.
     */
    public $errorMessage;

    /**
     * Document preview URL for modal.
     */
    public $previewUrl;

    /**
     * Current document name for modal.
     */
    public $previewName;

    /**
     * Whether to show the upload modal.
     */
    public bool $showUploadModal = false;

    /**
     * Whether to show the preview modal.
     */
    public bool $showPreviewModal = false;

    /**
     * Document ID pending deletion.
     */
    public ?string $documentIdToDelete = null;

    /**
     * Whether to show the delete confirmation modal.
     */
    public bool $showDeleteConfirmation = false;

    /**
     * Messages for the delete confirmation modal.
     */
    public array $deleteConfirmationMessages = [];

    /**
     * Search query for documents.
     */
    public $search = '';

    /**
     * Status filter for documents.
     */
    public $statusFilter = '';

    /**
     * Reset pagination when search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when status filter changes.
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Render the component.
     */
    public function render(DocumentService $documentService, DocumentRepositoryInterface $documentRepository): View
    {
        if (! app()->bound('ragbot.project')) {
            $this->errorMessage = 'Project context could not be resolved. Please try refreshing the page.';

            return view('ragbot::livewire.tenant.document-manager', [
                'documents' => collect(),
                'project' => (object) ['name' => 'Unknown'],
            ])->layout('ragbot::layouts.dashboard');
        }

        $project = app('ragbot.project');

        // Fetch stats for all documents in project via repository
        $stats = $documentRepository->getStats($project->id);

        $documents = $documentRepository->searchForProject(
            $project->id,
            $this->search,
            $this->statusFilter,
            10
        );

        // Calculate file sizes and fetch batch info for display
        $documents->each(function ($doc) {
            $disk = config('ragbot.storage.disk', 'local');
            try {
                if (Storage::disk($disk)->exists($doc->file_path)) {
                    $size = Storage::disk($disk)->size($doc->file_path);
                    $doc->display_size = $this->formatBytes($size);
                } else {
                    $doc->display_size = 'Unknown';
                }
            } catch (\Throwable $e) {
                $doc->display_size = 'Unknown';
            }

            // Fetch batch info to detect partial failures
            $doc->batch = null;
            if ($doc->processing_batch_id) {
                $doc->batch = Bus::findBatch($doc->processing_batch_id);
            }
        });

        return view('ragbot::livewire.tenant.document-manager', [
            'documents' => $documents,
            'project' => $project,
            'stats' => $stats,
        ])->layout('ragbot::layouts.dashboard');
    }

    /**
     * Format bytes into human readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Open preview modal for a document.
     */
    public function preview(string $documentId, DocumentRepositoryInterface $documentRepository): void
    {
        $project = app('ragbot.project');
        $this->previewUrl = route('ragbot.documents.preview', [
            'project_slug' => $project->slug,
            'document' => $documentId,
        ]);

        $document = $documentRepository->findById($documentId);
        $this->previewName = $document ? $document->name : 'Document Preview';

        $this->showPreviewModal = true;
    }

    /**
     * Handle document upload.
     */
    public function handleUpload(DocumentService $documentService): void
    {
        $this->validate([
            'selectedFile' => ['required', 'file', 'mimes:pdf,doc,docx,txt', 'max:10240'],
        ]);

        DB::beginTransaction();

        try {
            $documentService->store($this->selectedFile);

            DB::commit();
            $this->reset('selectedFile');
            $this->showUploadModal = false;
            $this->successMessage = 'Document uploaded successfully and is being processed.';
            $this->errorMessage = null;
        } catch (DocumentProcessingException $e) {
            DB::rollBack();
            $this->errorMessage = $e->getMessage();
            $this->successMessage = null;
            Log::error('Document upload failed: '.$e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->errorMessage = 'An unexpected error occurred during upload.';
            $this->successMessage = null;
            Log::error('Unexpected document upload error: '.$e->getMessage());
        }
    }

    /**
     * Handle manual document submission.
     */
    public function handleManualSubmit(DocumentService $documentService): void
    {
        $this->validate([
            'manualTitle' => ['required', 'string', 'max:255'],
            'manualContent' => ['required', 'string', 'min:10'],
        ]);

        DB::beginTransaction();

        try {
            // Strip HTML tags for processing if needed, but for now we'll store as text/plain
            // If we want to support full rich text indexing, we might need to handle HTML in TextExtractor
            $content = strip_tags($this->manualContent);

            $documentService->storeContent($this->manualTitle, $content);

            DB::commit();
            $this->reset(['manualTitle', 'manualContent']);
            $this->showUploadModal = false;
            $this->successMessage = 'Document created successfully and is being processed.';
            $this->errorMessage = null;
        } catch (DocumentProcessingException $e) {
            DB::rollBack();
            $this->errorMessage = $e->getMessage();
            $this->successMessage = null;
            Log::error('Manual document creation failed: '.$e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->errorMessage = 'An unexpected error occurred during document creation.';
            $this->successMessage = null;
            Log::error('Unexpected manual document error: '.$e->getMessage());
        }
    }

    /**
     * Open delete confirmation modal.
     */
    public function confirmDelete(string $documentId, DocumentRepositoryInterface $documentRepository): void
    {
        $document = $documentRepository->findById($documentId);
        if (! $document) {
            return;
        }

        $this->documentIdToDelete = $documentId;
        $this->deleteConfirmationMessages = [];

        $chatbots = $document->chatbots;

        if ($chatbots->isEmpty()) {
            $this->deleteConfirmationMessages[] = "Are you sure you want to permanently delete '{$document->name}'?";
        } else {
            foreach ($chatbots as $chatbot) {
                $docCount = $chatbot->documents()->count();
                if ($docCount === 1) {
                    $this->deleteConfirmationMessages[] = "the chatbot using this doc is also going to be deleted, Are you sure ({$chatbot->name})";
                } else {
                    $this->deleteConfirmationMessages[] = "Are you sure? This document is being used by chatbot {$chatbot->name}";
                }
            }
        }

        $this->showDeleteConfirmation = true;
    }

    /**
     * Delete a document.
     */
    public function delete(DocumentService $documentService): void
    {
        if (! $this->documentIdToDelete) {
            return;
        }

        DB::beginTransaction();

        try {
            $documentService->delete($this->documentIdToDelete);

            DB::commit();
            $this->successMessage = 'Document deleted successfully.';
            $this->errorMessage = null;
            $this->showDeleteConfirmation = false;
            $this->documentIdToDelete = null;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->errorMessage = 'Failed to delete document.';
            $this->successMessage = null;
            Log::error('Document deletion failed: '.$e->getMessage());
        }
    }

    /**
     * Clear all feedback messages.
     */
    public function clearMessages(): void
    {
        $this->reset(['successMessage', 'errorMessage']);
    }

    /**
     * Open the upload modal and reset state.
     */
    public function openUploadModal(): void
    {
        $this->reset(['manualTitle', 'manualContent', 'selectedFile', 'activeTab', 'errorMessage', 'successMessage']);
        $this->showUploadModal = true;
    }
}
