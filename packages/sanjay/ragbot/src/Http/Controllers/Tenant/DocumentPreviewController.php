<?php

namespace Sanjay\Ragbot\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Models\Document;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller for previewing document files.
 */
class DocumentPreviewController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected DocumentRepositoryInterface $documentRepository
    ) {}

    /**
     * Stream the document file for preview.
     */
    public function show(string $project_slug, string $documentId): StreamedResponse|Response
    {
        /** @var Document $document */
        $document = $this->documentRepository->findById($documentId);

        // Security check: ensure document belongs to current project context
        if (! app()->bound('ragbot.project') || $document->project_id !== app('ragbot.project')->id) {
            abort(403, 'Unauthorized access to document.');
        }

        $disk = config('ragbot.storage.disk', 'local');

        if (! Storage::disk($disk)->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk($disk)->response($document->file_path, $document->name, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="'.$document->name.'"',
        ]);
    }
}
