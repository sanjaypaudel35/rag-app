<?php

namespace Sanjay\Ragbot\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface;
use Sanjay\Ragbot\Models\Chunk;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Tests\TestCase;

class ChunkRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ChunkRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ChunkRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_chunks_by_document_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $documentA = Document::factory()->create(['project_id' => $projectA->id]);
        $documentB = Document::factory()->create(['project_id' => $projectB->id]);

        Chunk::factory()->count(3)->create([
            'project_id' => $projectA->id,
            'document_id' => $documentA->id
        ]);
        Chunk::factory()->count(2)->create([
            'project_id' => $projectB->id,
            'document_id' => $documentB->id
        ]);

        $chunksA = $this->repository->findByDocument($projectA->id, $documentA->id);
        $this->assertCount(3, $chunksA);

        $chunksB = $this->repository->findByDocument($projectB->id, $documentB->id);
        $this->assertCount(2, $chunksB);

        // Cross-check: Project A should not see Project B's document chunks
        $chunksAcross = $this->repository->findByDocument($projectA->id, $documentB->id);
        $this->assertCount(0, $chunksAcross);
    }
}
