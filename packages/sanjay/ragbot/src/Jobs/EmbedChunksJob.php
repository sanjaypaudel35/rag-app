<?php

namespace Sanjay\Ragbot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Document;
use Throwable;

/**
 * Job to orchestrate the embedding batch for a document.
 */
class EmbedChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public array $backoff = [10, 30];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Document $document
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DocumentRepositoryInterface $documentRepository): void
    {
        try {
            // 1. Validation: Ensure chunks exist
            $chunks = $this->document->chunks;

            if ($chunks->isEmpty()) {
                throw new \Exception("No chunks found for document: {$this->document->id}");
            }

            // 2. Update Status -> Processing
            $documentRepository->update($this->document->id, [
                'status' => DocumentStatus::Processing,
            ]);

            // 3. Prepare Batch
            $jobs = $chunks->map(fn ($chunk) => new EmbedChunkJob($chunk))->toArray();

            $documentId = $this->document->id;

            $batch = Bus::batch($jobs)
                ->name("Embedding: {$this->document->name}")
                ->allowFailures()
                ->finally(function ($batch) use ($documentId) {
                    $documentRepository = app(DocumentRepositoryInterface::class);
                    $document = $documentRepository->findById($documentId);

                    if (! $document) {
                        return;
                    }

                    if ($batch->failedJobs > 0) {
                        $documentRepository->update($document->id, [
                            'status' => DocumentStatus::Failed,
                            'error_message' => "Embedding failed for {$batch->failedJobs} out of {$batch->totalJobs} chunks.",
                        ]);
                    } else {
                        // Final transition to Completed
                        MarkDocumentAsCompletedJob::dispatch($document);
                    }
                })
                ->dispatch();

            $documentRepository->update($this->document->id, [
                'processing_batch_id' => $batch->id,
            ]);

        } catch (Throwable $e) {
            $documentRepository->update($this->document->id, [
                'status' => DocumentStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("EmbedChunksJob failed for document {$this->document->id}", [
                'stage' => 'dispatch_batch',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
