<?php

namespace Sanjay\Ragbot\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Contracts\Repositories\EmbeddingRepositoryInterface;
use Sanjay\Ragbot\Models\Chunk;
use Sanjay\Ragbot\Models\Embedding;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Tests\TestCase;

class EmbeddingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmbeddingRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(EmbeddingRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_embedding_by_chunk_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $chunkA = Chunk::factory()->create(['project_id' => $projectA->id]);
        $chunkB = Chunk::factory()->create(['project_id' => $projectB->id]);

        $embeddingA = Embedding::factory()->create([
            'project_id' => $projectA->id,
            'chunk_id' => $chunkA->id
        ]);
        $embeddingB = Embedding::factory()->create([
            'project_id' => $projectB->id,
            'chunk_id' => $chunkB->id
        ]);

        $foundA = $this->repository->findByChunk($projectA->id, $chunkA->id);
        $this->assertNotNull($foundA);
        $this->assertEquals($embeddingA->id, $foundA->id);

        $foundAcross = $this->repository->findByChunk($projectA->id, $chunkB->id);
        $this->assertNull($foundAcross);
    }
}
