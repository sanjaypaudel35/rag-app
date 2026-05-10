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
    public function test_it_can_find_chunks_by_document_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $documentA = Document::factory()->create(['project_id' => $projectA->id]);
        $documentB = Document::factory()->create(['project_id' => $projectB->id]);

        Chunk::factory()->count(3)->create([
            'project_id' => $projectA->id,
            'document_id' => $documentA->id,
        ]);
        Chunk::factory()->count(2)->create([
            'project_id' => $projectB->id,
            'document_id' => $documentB->id,
        ]);

        $this->app->instance('ragbot.project', $projectA);
        $chunksA = $this->repository->findByDocument($documentA->id);
        $this->assertCount(3, $chunksA);

        // Should not see Project B's document chunks
        $chunksAcross = $this->repository->findByDocument($documentB->id);
        $this->assertCount(0, $chunksAcross);

        $this->app->instance('ragbot.project', $projectB);
        $chunksB = $this->repository->findByDocument($documentB->id);
        $this->assertCount(2, $chunksB);
    }
}
