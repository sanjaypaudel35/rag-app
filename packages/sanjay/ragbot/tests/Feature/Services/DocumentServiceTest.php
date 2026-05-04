<?php

namespace Sanjay\Ragbot\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Sanjay\Ragbot\Exceptions\DocumentProcessingException;
use Sanjay\Ragbot\Jobs\ProcessDocumentJob;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Services\Tenant\DocumentService;
use Sanjay\Ragbot\Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Queue::fake();
    }

    /** @test */
    public function test_it_can_store_a_valid_document_and_dispatch_job(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        $service = app(DocumentService::class);
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $document = $service->store($file);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertEquals('test.pdf', $document->name);
        $this->assertEquals($project->id, $document->project_id);

        Storage::disk('local')->assertExists($document->file_path);
        Queue::assertPushed(ProcessDocumentJob::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    }

    /** @test */
    public function test_it_rejects_invalid_mime_types(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        $service = app(DocumentService::class);
        $file = UploadedFile::fake()->create('test.exe', 100, 'application/x-msdownload');

        $this->expectException(DocumentProcessingException::class);
        $this->expectExceptionMessage('Invalid file type');

        $service->store($file);
    }

    /** @test */
    public function test_it_can_delete_a_document_and_its_file(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        $service = app(DocumentService::class);
        $document = Document::factory()->create([
            'project_id' => $project->id,
            'file_path' => "ragbot/{$project->id}/documents/test.pdf",
        ]);
        Storage::disk('local')->put($document->file_path, 'content');

        $service->delete($document->id);

        $this->assertDatabaseMissing('rag_documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing($document->file_path);
    }

    /** @test */
    public function test_it_can_list_documents_for_a_project(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        Document::factory()->count(3)->create(['project_id' => $project->id]);

        $otherProject = Project::factory()->create();
        Document::factory()->create(['project_id' => $otherProject->id]);

        $service = app(DocumentService::class);
        $documents = $service->listForProject();

        $this->assertCount(3, $documents);
        $documents->each(function ($doc) use ($project) {
            $this->assertEquals($project->id, $doc->project_id);
        });
    }
}
