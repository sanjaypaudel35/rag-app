<?php

namespace Sanjay\Ragbot\Tests\Feature\Livewire\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Document;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\RagbotUser;
use Sanjay\Ragbot\Tests\TestCase;

class ChatbotManagerTest extends TestCase
{
    use RefreshDatabase;

    protected Project $project;

    protected RagbotUser $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Project::factory()->create();
        app()->instance('ragbot.project', $this->project);

        $this->user = RagbotUser::factory()->create(['project_id' => $this->project->id]);
    }

    /** @test */
    public function test_it_renders_successfully(): void
    {
        Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.chatbot-manager')
            ->assertStatus(200)
            ->assertSee('Chatbot Key Management');
    }

    /** @test */
    public function test_it_can_create_a_chatbot(): void
    {
        $document = Document::factory()->create([
            'project_id' => $this->project->id,
            'status' => DocumentStatus::Completed,
            'name' => 'Knowledge Doc',
        ]);

        $test = Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.chatbot-manager')
            ->set('name', 'My New Chatbot')
            ->set('selectedDocuments', [$document->id])
            ->call('createChatbot')
            ->assertHasNoErrors()
            ->assertSee('Chatbot created successfully');

        $rawKey = $test->get('newlyGeneratedKey');
        $this->assertStringStartsWith('rb_c_', $rawKey);

        $this->assertDatabaseHas('rag_chatbots', [
            'name' => 'My New Chatbot',
            'project_id' => $this->project->id,
            'api_key' => hash('sha256', $rawKey),
        ]);

        $chatbot = Chatbot::where('name', 'My New Chatbot')->first();
        $this->assertTrue($chatbot->documents->contains($document));
    }

    /** @test */
    public function test_it_can_regenerate_chatbot_api_key(): void
    {
        $chatbot = Chatbot::factory()->create([
            'project_id' => $this->project->id,
            'api_key' => hash('sha256', 'old_key'),
        ]);

        $test = Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.chatbot-manager')
            ->call('regenerateKey', $chatbot->id)
            ->assertHasNoErrors()
            ->assertSet('newlyGeneratedKey', function ($key) {
                return str_starts_with($key, 'rb_c_');
            });

        $rawKey = $test->get('newlyGeneratedKey');
        $chatbot->refresh();
        $this->assertEquals(hash('sha256', $rawKey), $chatbot->api_key);
        $this->assertEquals(64, strlen($chatbot->api_key));
    }

    /** @test */
    public function test_it_can_delete_a_chatbot(): void
    {
        $chatbot = Chatbot::factory()->create(['project_id' => $this->project->id]);

        Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.chatbot-manager')
            ->call('deleteChatbot', $chatbot->id)
            ->assertSee('Chatbot deleted successfully');

        $this->assertSoftDeleted('rag_chatbots', ['id' => $chatbot->id]);
    }

    /** @test */
    public function test_it_can_start_testing_and_initialize(): void
    {
        $chatbot = Chatbot::factory()->create(['project_id' => $this->project->id, 'name' => 'Test Bot']);

        Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.chatbot-manager')
            ->call('startTesting', $chatbot->id)
            ->assertSet('testingChatbotId', $chatbot->id)
            ->assertSee('Testing: Test Bot')
            ->call('initializeChat')
            ->assertSet('isInitialized', true)
            ->assertSee('Chat initialized with Chatbot API Key.');
    }

    /** @test */
    public function test_it_can_regenerate_project_master_key(): void
    {
        $oldHash = $this->project->api_key;

        $test = Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.chatbot-manager')
            ->call('regenerateProjectKey')
            ->assertSee('Project API key regenerated successfully.');

        $this->project->refresh();
        $this->assertNotEquals($oldHash, $this->project->api_key);
        $this->assertEquals(64, strlen($this->project->api_key));

        $rawKey = $test->get('newProjectKey');
        $this->assertStringStartsWith('rb_p_', $rawKey);
        $this->assertEquals(hash('sha256', $rawKey), $this->project->api_key);
    }
}
