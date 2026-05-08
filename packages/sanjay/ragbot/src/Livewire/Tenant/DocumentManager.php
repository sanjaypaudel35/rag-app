<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
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
     * Render the component.
     */
    public function render(DocumentService $documentService): View
    {
        if (! app()->bound('ragbot.project')) {
            $this->errorMessage = 'Project context could not be resolved. Please try refreshing the page.';

            return view('ragbot::livewire.tenant.document-manager', [
                'documents' => collect(),
                'project' => (object) ['name' => 'Unknown'],
            ])->layout('ragbot::layouts.dashboard');
        }

        $project = app('ragbot.project');
        $documents = $documentService->listForProject();

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
    public function preview(string $documentId): void
    {
        $project = app('ragbot.project');
        $this->previewUrl = route('ragbot.documents.preview', [
            'project_slug' => $project->slug,
            'document' => $documentId,
        ]);

        $document = Document::find($documentId);
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
     * Delete a document.
     */
    public function delete(DocumentService $documentService, string $documentId): void
    {
        DB::beginTransaction();

        try {
            $documentService->delete($documentId);

            DB::commit();
            $this->successMessage = 'Document deleted successfully.';
            $this->errorMessage = null;
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
}
