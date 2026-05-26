<?php

namespace Sanjay\Ragbot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Services\Tenant\ChunkingService;
use Sanjay\Ragbot\Support\TextExtractor;
use Throwable;

/**
 * Job to extract text and create chunks for a document.
 */
class ChunkDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        public Document $document
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        ChunkingService $chunkingService,
        TextExtractor $extractor,
        DocumentRepositoryInterface $documentRepository,
        ChunkRepositoryInterface $chunkRepository
    ): void {
        try {
            // 1. Validation: Skip if already completed
            if ($this->document->status === DocumentStatus::Completed) {
                Log::info("Document {$this->document->id} already processed. Skipping chunking.");

                return;
            }

            // 2. State transition -> Processing
            $documentRepository->update($this->document->id, ['status' => DocumentStatus::Processing]);

            // 3. Extraction
            $text = $extractor->extract($this->document->file_path, $this->document->mime_type);

            if (empty(trim($text))) {
                throw new \Exception("Extracted text is empty for document: {$this->document->id}");
            }

            // 4. Chunking
            $chunks = $chunkingService->chunk($text);

            if (empty($chunks)) {
                throw new \Exception("Chunking returned zero chunks for document: {$this->document->id}");
            }

            // 5. Transactional Storage
            DB::beginTransaction();
            try {
                // Idempotency: Remove existing chunks if any
                $this->document->chunks()->delete();

                foreach ($chunks as $index => $content) {
                    $chunkRepository->create([
                        'project_id' => $this->document->project_id,
                        'document_id' => $this->document->id,
                        'content' => $content,
                        'chunk_index' => $index,
                        'token_count' => count(explode(' ', $content)), // Basic tokenization
                    ]);
                }

                $documentRepository->update($this->document->id, ['status' => DocumentStatus::Processing]);

                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw $e;
            }

            // 6. Dispatch Next Stage (afterCommit ensures DB is synced)
            EmbedChunksJob::dispatch($this->document)->afterCommit();

        } catch (Throwable $e) {
            $documentRepository->update($this->document->id, [
                'status' => DocumentStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("ChunkDocumentJob failed for document {$this->document->id}", [
                'stage' => 'chunking',
                'error' => $e->getMessage(),
                'file' => $this->document->file_path,
            ]);

            throw $e;
        }
    }
}
