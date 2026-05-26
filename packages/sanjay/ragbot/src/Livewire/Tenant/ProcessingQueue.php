<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Jobs\ProcessDocumentJob;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;

class ProcessingQueue extends Component
{
    /**
     * The project instance.
     */
    public Project $project;

    /**
     * Failed jobs for the selected document.
     */
    public array $selectedFailedJobs = [];

    /**
     * Name of the document being inspected for failed jobs.
     */
    public string $selectedDocumentName = '';

    /**
     * ID of the document being inspected for failed jobs.
     */
    public string $selectedDocumentId = '';

    /**
     * Total number of jobs in the batch.
     */
    public int $totalBatchJobs = 0;

    /**
     * Number of failed jobs in the batch.
     */
    public int $failedBatchJobs = 0;

    /**
     * Whether to show the failed jobs modal.
     */
    public bool $showFailedJobsModal = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->project = app('ragbot.project');
    }

    /**
     * Get the documents currently in the processing queue.
     */
    #[Computed]
    public function documents()
    {
        return app(DocumentRepositoryInterface::class)->getRecentForProject(
            $this->project->id,
            [
                DocumentStatus::Pending->value,
                DocumentStatus::Processing->value,
                DocumentStatus::Failed->value,
                DocumentStatus::Completed->value,
            ],
            10
        )->map(function ($document) {
            $batch = null;
            if ($document->processing_batch_id) {
                $batch = Bus::findBatch($document->processing_batch_id);
            }

            return (object) [
                'id' => $document->id,
                'name' => $document->name,
                'status' => $document->status,
                'error_message' => $document->error_message,
                'batch' => $batch,
                'created_at' => $document->created_at,
            ];
        });
    }

    /**
     * View failed jobs for a specific document.
     */
    public function viewFailedJobs(string $documentId, DocumentRepositoryInterface $documentRepository): void
    {
        $document = $documentRepository->findById($documentId);
        $this->selectedDocumentName = $document->name;
        $this->selectedDocumentId = $document->id;
        $this->totalBatchJobs = 0;
        $this->failedBatchJobs = 0;

        $failedJobUuids = [];

        // 1. Get failed jobs from the batch if it exists
        if ($document->processing_batch_id) {
            $batch = Bus::findBatch($document->processing_batch_id);
            if ($batch) {
                $this->totalBatchJobs = $batch->totalJobs;
                $this->failedBatchJobs = $batch->failedJobs;
                if (! empty($batch->failedJobIds)) {
                    $failedJobUuids = array_merge($failedJobUuids, $batch->failedJobIds);
                }
            }
        }

        // 2. Query failed_jobs for any jobs related to this document ID in the payload
        // This covers ProcessDocumentJob, ChunkDocumentJob, and EmbedChunksJob
        $individualFailedJobs = DB::table('failed_jobs')
            ->where('payload', 'like', '%'.$document->id.'%')
            ->get();

        $this->selectedFailedJobs = collect($failedJobUuids)
            ->map(function ($uuid) {
                return DB::table('failed_jobs')->where('uuid', $uuid)->first();
            })
            ->filter()
            ->concat($individualFailedJobs)
            ->unique('uuid') // Prevent duplicates between batch IDs and payload search
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'uuid' => $job->uuid,
                    'payload' => json_decode($job->payload, true),
                    'exception' => $job->exception,
                    'failed_at' => $job->failed_at,
                ];
            })
            ->sortByDesc('failed_at')
            ->values()
            ->toArray();

        // If no batch but we found individual failed jobs, update counts
        if ($this->totalBatchJobs === 0 && count($this->selectedFailedJobs) > 0) {
            $this->totalBatchJobs = count($this->selectedFailedJobs);
            $this->failedBatchJobs = count($this->selectedFailedJobs);
        }

        $this->showFailedJobsModal = true;
    }

    /**
     * Retry processing for a specific document.
     */
    public function retry(string $documentId, DocumentRepositoryInterface $documentRepository): void
    {
        $document = $documentRepository->findById($documentId);

        DB::beginTransaction();
        try {
            // Reset state
            $documentRepository->update($document->id, [
                'status' => DocumentStatus::Pending,
                'error_message' => null,
                'processing_batch_id' => null,
            ]);

            // Clear previous chunks to ensure idempotency
            $document->chunks()->delete();

            DB::commit();

            // Start processing again
            ProcessDocumentJob::dispatch($document);

            session()->flash('success', "Processing restarted for {$document->name}");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to retry document processing: '.$e->getMessage());
            session()->flash('error', 'Failed to restart processing.');
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('ragbot::livewire.tenant.processing-queue')
            ->layout('ragbot::layouts.dashboard');
    }
}
