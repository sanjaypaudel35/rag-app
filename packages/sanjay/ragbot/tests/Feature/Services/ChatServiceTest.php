<?php

namespace Sanjay\Ragbot\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Conversation;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;
use Sanjay\Ragbot\Services\Tenant\ChatService;
use Sanjay\Ragbot\Tests\TestCase;

class ChatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Project $project;

    protected Chatbot $chatbot;

    protected ChatService $chatService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Project::factory()->create();

        ProjectSetting::factory()->create([
            'project_id' => $this->project->id,
            'llm_provider' => 'openai',
            'llm_api_key' => 'test-key',
        ]);

        $this->chatbot = Chatbot::factory()->create(['project_id' => $this->project->id]);
        $this->chatService = app(ChatService::class);

        // Bind project and chatbot to container
        app()->instance('ragbot.project', $this->project);
        app()->instance('ragbot.chatbot', $this->chatbot);
    }

    /** @test */
    public function test_it_stores_user_and_assistant_messages(): void
    {
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'AI Response']]],
            ]),
            'https://api.openai.com/v1/embeddings' => Http::response([
                'data' => [['embedding' => array_fill(0, 1536, 0.1)]],
            ]),
        ]);

        $response = $this->chatService->chat($this->project, 'session-abc', 'Hello AI');

        $this->assertEquals('AI Response', $response);

        $this->assertDatabaseHas('rag_messages', [
            'content' => 'Hello AI',
            'role' => 'user',
        ]);

        $this->assertDatabaseHas('rag_messages', [
            'content' => 'AI Response',
            'role' => 'assistant',
        ]);
    }

    /** @test */
    public function test_it_creates_conversation_if_session_is_new(): void
    {
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'AI Response']]],
            ]),
            'https://api.openai.com/v1/embeddings' => Http::response([
                'data' => [['embedding' => array_fill(0, 1536, 0.1)]],
            ]),
        ]);

        $this->assertEquals(0, Conversation::count());

        $this->chatService->chat($this->project, 'new-session', 'Initial message');

        $this->assertEquals(1, Conversation::count());
        $this->assertDatabaseHas('rag_conversations', [
            'session_id' => 'new-session',
            'chatbot_id' => $this->chatbot->id,
        ]);
    }
}
