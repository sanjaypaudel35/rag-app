<?php

namespace Sanjay\Ragbot\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Chunk;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Services\Tenant\ChunkingService;
use Sanjay\Ragbot\Support\TextExtractor;

class ChunkDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Document $document) {}

    /**
     * Execute the job.
     */
    public function handle(ChunkingService $chunkingService, TextExtractor $extractor): void
    {
        try {
            $text = $extractor->extract($this->document->file_path, $this->document->mime_type);
            $chunks = $chunkingService->chunk($text);

            DB::transaction(function () use ($chunks) {
                // Remove existing chunks if any (idempotency)
                $this->document->chunks()->delete();

                foreach ($chunks as $index => $content) {
                    Chunk::create([
                        'project_id' => $this->document->project_id,
                        'document_id' => $this->document->id,
                        'content' => $content,
                        'chunk_index' => $index,
                        'token_count' => count(explode(' ', $content)), // Simple token count estimation
                    ]);
                }
            });

            EmbedChunksJob::dispatch($this->document);
        } catch (Exception $e) {
            $this->document->update([
                'status' => DocumentStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
