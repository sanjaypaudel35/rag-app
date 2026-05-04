<?php

namespace Sanjay\Ragbot\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Tests\TestCase;

class DocumentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected DocumentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(DocumentRepositoryInterface::class);
    }

    /** @test */
    public function test_it_scopes_all_queries_by_project_id(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        Document::factory()->create(['project_id' => $projectA->id]);
        Document::factory()->create(['project_id' => $projectB->id]);

        // Scope to Project A
        app()->instance('ragbot.project', $projectA);
        $this->assertCount(1, $this->repository->all());
        $this->assertEquals($projectA->id, $this->repository->all()->first()->project_id);

        // Scope to Project B
        app()->instance('ragbot.project', $projectB);
        $this->assertCount(1, $this->repository->all());
        $this->assertEquals($projectB->id, $this->repository->all()->first()->project_id);
    }

    /** @test */
    public function test_it_can_find_documents_by_status(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        Document::factory()->create(['project_id' => $project->id, 'status' => 'pending']);
        Document::factory()->create(['project_id' => $project->id, 'status' => 'completed']);

        $this->assertCount(1, $this->repository->findByStatus('pending'));
        $this->assertCount(1, $this->repository->findByStatus('completed'));
    }

    /** @test */
    public function test_it_can_update_document_status(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        $document = Document::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

        $this->repository->updateStatus($document->id, 'completed');

        $this->assertEquals('completed', $document->fresh()->status->value);
    }
}
