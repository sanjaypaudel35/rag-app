<?php

namespace Sanjay\Ragbot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Document;

/**
 * Job for processing an uploaded document.
 */
class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Document $document) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Idempotency check: skip if status is already completed
        if ($this->document->status === DocumentStatus::Completed) {
            return;
        }

        $this->document->update([
            'status' => DocumentStatus::Processing,
        ]);

        ChunkDocumentJob::dispatch($this->document);
    }
}
