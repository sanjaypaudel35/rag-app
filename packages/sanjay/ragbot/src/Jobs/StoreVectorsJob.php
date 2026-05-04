<?php

namespace Sanjay\Ragbot\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Document;

class StoreVectorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  array<string, array<float>>  $embeddings
     */
    public function __construct(
        public Document $document,
        public array $embeddings
    ) {}

    /**
     * Execute the job.
     */
    public function handle(VectorStoreInterface $vectorStore): void
    {
        try {
            $project = $this->document->project;

            foreach ($this->embeddings as $chunkId => $vector) {
                $vectorStore->store($project, $chunkId, $vector);
            }

            $this->document->update([
                'status' => DocumentStatus::Completed,
            ]);
        } catch (Exception $e) {
            $this->document->update([
                'status' => DocumentStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
