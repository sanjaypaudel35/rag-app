<?php

namespace Sanjay\Ragbot\Tests\Feature\Livewire\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\RagbotUser;
use Sanjay\Ragbot\Services\Tenant\ProjectSettingsService;
use Sanjay\Ragbot\Tests\TestCase;

class DocumentManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        Http::fake([
            'api.openai.com/*' => Http::response([
                'data' => [
                    [
                        'embedding' => array_fill(0, 1536, 0.1),
                    ],
                ],
            ], 200),
        ]);
    }

    /** @test */
    public function test_it_renders_successfully(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        $user = RagbotUser::factory()->create(['project_id' => $project->id]);

        Livewire::actingAs($user, 'ragbot')
            ->test('ragbot.document-manager')
            ->assertStatus(200);
    }

    /** @test */
    public function test_it_can_upload_a_document(): void
    {
        $project = Project::factory()->create();
        app(ProjectSettingsService::class)->getForProject($project);
        app()->instance('ragbot.project', $project);

        $user = RagbotUser::factory()->create(['project_id' => $project->id]);
        $file = UploadedFile::fake()->createWithContent('test.txt', 'This is some sample text content for extraction.');

        Livewire::actingAs($user, 'ragbot')
            ->test('ragbot.document-manager')
            ->set('selectedFile', $file)
            ->call('handleUpload')
            ->assertSee('Document uploaded successfully');

        $this->assertDatabaseHas('rag_documents', [
            'name' => 'test.txt',
            'project_id' => $project->id,
        ]);
    }

    /** @test */
    public function test_it_can_delete_a_document(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        $user = RagbotUser::factory()->create(['project_id' => $project->id]);
        $document = Document::factory()->create(['project_id' => $project->id]);

        Livewire::actingAs($user, 'ragbot')
            ->test('ragbot.document-manager')
            ->call('delete', $document->id)
            ->assertSee('Document deleted successfully');

        $this->assertDatabaseMissing('rag_documents', ['id' => $document->id]);
    }
}
