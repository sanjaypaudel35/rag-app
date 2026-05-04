<?php

namespace Sanjay\Ragbot\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Jobs\ProcessDocumentJob;
use Sanjay\Ragbot\Models\Chunk;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Embedding;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Services\Tenant\ChunkingService;
use Sanjay\Ragbot\Services\Tenant\MysqlVectorStoreService;
use Sanjay\Ragbot\Tests\TestCase;

class DocumentProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function test_chunking_service_splits_text_correctly_with_overlap()
    {
        $service = new ChunkingService;
        $text = 'This is a long text that should be split into multiple chunks with some overlap between them.';

        // chunk size 20, overlap 5
        $chunks = $service->chunk($text, 20, 5);

        $this->assertNotEmpty($chunks);
        $this->assertEquals('This is a long text ', $chunks[0]);
        // Overlap of 5 from "text "
        $this->assertStringStartsWith('text ', $chunks[1]);
    }

    /** @test */
    public function test_vector_store_service_stores_and_retrieves_vectors()
    {
        $project = Project::factory()->create();
        $document = Document::factory()->for($project)->create();
        $chunk = Chunk::factory()->for($document)->for($project)->create();

        $service = new MysqlVectorStoreService;
        $vector = array_fill(0, 1536, 0.1);

        $service->store($project, $chunk->id, $vector);

        $this->assertDatabaseHas('rag_embeddings', [
            'project_id' => $project->id,
            'chunk_id' => $chunk->id,
        ]);

        $results = $service->search($project, $vector, 1);

        $this->assertCount(1, $results);
        $this->assertEquals($chunk->id, $results->first()->id);
    }

    /** @test */
    public function test_document_processing_pipeline_transitions_correctly()
    {
        // Don't fake queue here to let it update the status, or use sync
        config(['queue.default' => 'sync']);

        $project = Project::factory()->create();
        $document = Document::factory()->for($project)->create([
            'status' => DocumentStatus::Pending,
            'file_path' => 'test.txt',
            'mime_type' => 'text/plain',
        ]);

        Storage::disk('local')->put('test.txt', 'Sample document content for testing pipeline.');

        // We use a mock or partial to avoid full pipeline if we just want to test ONE step
        // But since they are chained, let's just assert the first step works

        ProcessDocumentJob::dispatch($document);

        $document->refresh();
        // It will be Completed if full pipeline runs sync
        $this->assertEquals(DocumentStatus::Completed, $document->status);
    }

    /** @test */
    public function test_full_pipeline_execution_works()
    {
        // Don't fake queue here to test the actual execution (using sync driver if possible)
        config(['queue.default' => 'sync']);

        $project = Project::factory()->create();
        $document = Document::factory()->for($project)->create([
            'status' => DocumentStatus::Pending,
            'file_path' => 'test.txt',
            'mime_type' => 'text/plain',
        ]);

        Storage::disk('local')->put('test.txt', 'Sample document content for testing pipeline.');

        ProcessDocumentJob::dispatch($document);

        $document->refresh();
        $this->assertEquals(DocumentStatus::Completed, $document->status);
        $this->assertGreaterThan(0, $document->chunks()->count());
        $this->assertGreaterThan(0, Embedding::where('project_id', $project->id)->count());
    }
}
