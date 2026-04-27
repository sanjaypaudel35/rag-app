<?php

namespace Sanjay\Ragbot\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Tests\TestCase;
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
    public function test_it_can_find_a_document_by_id_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $documentA = Document::factory()->create(["project_id" => $projectA->id]);
        $documentB = Document::factory()->create(["project_id" => $projectB->id]);

        $this->app->instance("ragbot.project", $projectA);
        $found = $this->repository->findById($documentA->id);
        $this->assertNotNull($found);
        $this->assertEquals($documentA->id, $found->id);

        $not_found = $this->repository->findById($documentB->id);
        $this->assertNull($not_found);

        $this->app->instance("ragbot.project", $projectB);
        $foundB = $this->repository->findById($documentB->id);
        $this->assertNotNull($foundB);
        $this->assertEquals($documentB->id, $foundB->id);
    }

    /** @test */
    public function test_it_can_all_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        Document::factory()->count(3)->create(["project_id" => $projectA->id]);
        Document::factory()->count(2)->create(["project_id" => $projectB->id]);

        $this->app->instance("ragbot.project", $projectA);
        $docsA = $this->repository->all();
        $this->assertCount(3, $docsA);

        $this->app->instance("ragbot.project", $projectB);
        $docsB = $this->repository->all();
        $this->assertCount(2, $docsB);
    }

    /** @test */
    public function test_it_can_find_by_status_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        Document::factory()->create([
            "project_id" => $projectA->id,
            "status" => DocumentStatus::Completed
        ]);
        Document::factory()->create([
            "project_id" => $projectA->id,
            "status" => DocumentStatus::Pending
        ]);
        Document::factory()->create([
            "project_id" => $projectB->id,
            "status" => DocumentStatus::Completed
        ]);

        $this->app->instance("ragbot.project", $projectA);
        $completedA = $this->repository->findByStatus(DocumentStatus::Completed->value);
        $this->assertCount(1, $completedA);

        $this->app->instance("ragbot.project", $projectB);
        $completedB = $this->repository->findByStatus(DocumentStatus::Completed->value);
        $this->assertCount(1, $completedB);
    }

    /** @test */
    public function test_it_can_update_status_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $documentA = Document::factory()->create(["project_id" => $projectA->id, "status" => DocumentStatus::Pending]);

        $this->app->instance("ragbot.project", $projectA);
        $updated = $this->repository->updateStatus($documentA->id, DocumentStatus::Completed->value);
        $this->assertTrue($updated);
        $this->assertEquals(DocumentStatus::Completed, $documentA->refresh()->status);

        $this->app->instance("ragbot.project", $projectB);
        $updatedWrongProject = $this->repository->updateStatus($documentA->id, DocumentStatus::Failed->value);
        $this->assertFalse($updatedWrongProject);
        $this->assertEquals(DocumentStatus::Completed, $documentA->refresh()->status);
    }
}
