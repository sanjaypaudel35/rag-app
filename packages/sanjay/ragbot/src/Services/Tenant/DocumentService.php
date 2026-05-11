<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Exceptions\DocumentProcessingException;
use Sanjay\Ragbot\Jobs\ProcessDocumentJob;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Throwable;

/**
 * Handles document storage and dispatches processing pipeline.
 */
class DocumentService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected DocumentRepositoryInterface $documentRepository
    ) {}

    /**
     * Store a new document for the current project.
     *
     *
     * @throws DocumentProcessingException
     */
    public function store(UploadedFile $file): Document
    {
        $project = app('ragbot.project');
        $disk = config('ragbot.storage.disk', 'local');

        // Validate mime type
        $allowedMimes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/msword'];
        if (! in_array($file->getMimeType(), $allowedMimes)) {
            throw new DocumentProcessingException('Invalid file type. Only PDF, DOCX, and TXT are allowed.');
        }

        try {
            // Store file to storage/ragbot/{project_id}/documents/
            $path = $file->store("ragbot/{$project->slug}/documents", $disk);

            if (! $path) {
                throw new DocumentProcessingException('Failed to store file.');
            }

            // Create Document record via DocumentRepository
            /** @var Document $document */
            $document = $this->documentRepository->create([
                'project_id' => $project->id,
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'status' => DocumentStatus::Pending,
            ]);

            // Dispatch ProcessDocumentJob
            ProcessDocumentJob::dispatch($document);

            return $document;
        } catch (Throwable $e) {
            if (isset($path)) {
                Storage::disk($disk)->delete($path);
            }
            throw new DocumentProcessingException('Document processing failed: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Store a new document from manual content.
     *
     * @throws DocumentProcessingException
     */
    public function storeContent(string $name, string $content): Document
    {
        $project = app('ragbot.project');
        $disk = config('ragbot.storage.disk', 'local');

        try {
            // Generate a filename if not provided
            $filename = str_replace(['/', '\\'], '-', $name);
            if (! str_ends_with($filename, '.txt')) {
                $filename .= '.txt';
            }

            $path = "ragbot/{$project->slug}/documents/".now()->timestamp.'-'.$filename;

            // Store content to storage/ragbot/{project_id}/documents/
            if (! Storage::disk($disk)->put($path, $content)) {
                throw new DocumentProcessingException('Failed to store document content.');
            }

            // Create Document record via DocumentRepository
            /** @var Document $document */
            $document = $this->documentRepository->create([
                'project_id' => $project->id,
                'name' => $name,
                'file_path' => $path,
                'mime_type' => 'text/plain',
                'status' => DocumentStatus::Pending,
            ]);

            // Dispatch ProcessDocumentJob
            ProcessDocumentJob::dispatch($document);

            return $document;
        } catch (Throwable $e) {
            if (isset($path)) {
                Storage::disk($disk)->delete($path);
            }
            throw new DocumentProcessingException('Document content processing failed: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete a document.
     */
    public function delete(string $documentId): void
    {
        $document = $this->documentRepository->findById($documentId);
        $disk = config('ragbot.storage.disk', 'local');

        // Delete file from storage
        Storage::disk($disk)->delete($document->file_path);

        // Delete record
        $this->documentRepository->delete($documentId);
    }

    /**
     * List all documents for the current project.
     *
     * @return Collection<int, Document>
     */
    public function listForProject(): Collection
    {
        return Document::withCount('chunks')->latest()->get();
    }
}
