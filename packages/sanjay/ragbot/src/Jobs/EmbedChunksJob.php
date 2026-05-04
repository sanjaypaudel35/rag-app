<?php

namespace Sanjay\Ragbot\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Document;

class EmbedChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Document $document) {}

    /**
     * Execute the job.
     */
    public function handle(EmbeddingInterface $embeddingService): void
    {
        try {
            $chunks = $this->document->chunks;
            $embeddings = [];

            foreach ($chunks as $chunk) {
                $embeddings[$chunk->id] = $embeddingService->embed($chunk->content);
            }

            StoreVectorsJob::dispatch($this->document, $embeddings);
        } catch (Exception $e) {
            $this->document->update([
                'status' => DocumentStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
