<?php

namespace Sanjay\Ragbot\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Models\Chunk;
use Throwable;

/**
 * Job to generate embedding for a single chunk and store it in the vector database.
 */
class EmbedChunkJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Chunk $chunk
    ) {}

    /**
     * Execute the job.
     */
    public function handle(EmbeddingInterface $embeddingService, VectorStoreInterface $vectorStore): void
    {
        // Fail if the batch has been cancelled
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Re-establish project context in the container for the background job
        if (! app()->bound('ragbot.project')) {
            app()->instance('ragbot.project', $this->chunk->project);
        }

        try {
            // 1. Generate Embedding (API Call)
            $vector = $embeddingService->embed($this->chunk->content, $this->chunk->project);

            if (empty($vector)) {
                throw new \Exception("Generated embedding is empty for chunk: {$this->chunk->id}");
            }

            // 2. Store Vector (DB Transaction)
            DB::beginTransaction();
            try {
                $vectorStore->store($this->chunk->project, $this->chunk->id, $vector);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (Throwable $e) {
            Log::error("Embedding failed for chunk {$this->chunk->id}", [
                'document_id' => $this->chunk->document_id,
                'chunk_id' => $this->chunk->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
