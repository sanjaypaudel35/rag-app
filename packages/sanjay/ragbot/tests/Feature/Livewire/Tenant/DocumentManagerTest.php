<?php

namespace Sanjay\Ragbot\Tests\Feature\Livewire\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\RagbotUser;
use Sanjay\Ragbot\Tests\TestCase;

class DocumentManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    /** @test */
    public function test_it_renders_successfully(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        $user = RagbotUser::factory()->create(['project_id' => $project->id]);

        Livewire::actingAs($user, 'ragbot')
            ->test('ragbot.document-manager')
            ->assertStatus(200)
            ->assertSee($project->name);
    }

    /** @test */
    public function test_it_can_upload_a_document(): void
    {
        $project = Project::factory()->create();
        app()->instance('ragbot.project', $project);

        $user = RagbotUser::factory()->create(['project_id' => $project->id]);
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        Livewire::actingAs($user, 'ragbot')
            ->test('ragbot.document-manager')
            ->set('file', $file)
            ->call('upload')
            ->assertSet('file', null)
            ->assertSee('Document uploaded successfully');

        $this->assertDatabaseHas('rag_documents', [
            'name' => 'test.pdf',
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
