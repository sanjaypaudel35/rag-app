<?php

namespace Sanjay\Ragbot\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Tests\TestCase;
use Sanjay\Ragbot\Enums\DocumentStatus;

class DocumentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected DocumentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(DocumentRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_a_document_by_id_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $documentA = Document::factory()->create(['project_id' => $projectA->id]);
        $documentB = Document::factory()->create(['project_id' => $projectB->id]);

        $found = $this->repository->findById($documentA->id, $projectA->id);
        $this->assertNotNull($found);
        $this->assertEquals($documentA->id, $found->id);

        $not_found = $this->repository->findById($documentA->id, $projectB->id);
        $this->assertNull($not_found);
    }

    /** @test */
    public function it_can_all_for_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        Document::factory()->count(3)->create(['project_id' => $projectA->id]);
        Document::factory()->count(2)->create(['project_id' => $projectB->id]);

        $docsA = $this->repository->allForProject($projectA->id);
        $this->assertCount(3, $docsA);

        $docsB = $this->repository->allForProject($projectB->id);
        $this->assertCount(2, $docsB);
    }

    /** @test */
    public function it_can_find_by_status_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        Document::factory()->create([
            'project_id' => $projectA->id,
            'status' => DocumentStatus::Processed->value
        ]);
        Document::factory()->create([
            'project_id' => $projectA->id,
            'status' => DocumentStatus::Pending->value
        ]);
        Document::factory()->create([
            'project_id' => $projectB->id,
            'status' => DocumentStatus::Processed->value
        ]);

        $processedA = $this->repository->findByStatus($projectA->id, DocumentStatus::Processed->value);
        $this->assertCount(1, $processedA);
    }

    /** @test */
    public function it_can_update_status_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $documentA = Document::factory()->create(['project_id' => $projectA->id, 'status' => DocumentStatus::Pending->value]);

        $updated = $this->repository->updateStatus($documentA->id, $projectA->id, DocumentStatus::Processed->value);
        $this->assertTrue($updated);
        $this->assertEquals(DocumentStatus::Processed, $documentA->refresh()->status);

        $updatedWrongProject = $this->repository->updateStatus($documentA->id, $projectB->id, DocumentStatus::Failed->value);
        $this->assertFalse($updatedWrongProject);
        $this->assertEquals(DocumentStatus::Processed, $documentA->refresh()->status);
    }
}
