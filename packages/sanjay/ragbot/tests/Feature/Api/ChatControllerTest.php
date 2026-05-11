<?php

namespace Sanjay\Ragbot\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;
use Sanjay\Ragbot\Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Project $project;

    protected Chatbot $chatbot;

    protected string $rawKey = 'rb_c_test_key_123';

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Project::factory()->create();

        ProjectSetting::factory()->create([
            'project_id' => $this->project->id,
            'llm_provider' => 'openai',
            'llm_api_key' => 'test-key',
        ]);

        $this->chatbot = Chatbot::factory()->create([
            'project_id' => $this->project->id,
            'api_key' => hash('sha256', $this->rawKey),
        ]);
    }

    /** @test */
    public function test_it_returns_401_without_api_key(): void
    {
        $response = $this->postJson('/ragbot/api/v1/chat', [
            'message' => 'Hello',
            'session_id' => 'session-123',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function test_it_returns_200_with_valid_request(): void
    {
        // Mock LLM and Embeddings
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Hello, I am your assistant.']],
                ],
            ]),
            'https://api.openai.com/v1/embeddings' => Http::response([
                'data' => [['embedding' => array_fill(0, 1536, 0.1)]],
            ]),
        ]);

        $response = $this->withHeaders([
            'X-Api-Key' => $this->rawKey,
        ])->postJson('/ragbot/api/v1/chat', [
            'message' => 'Who are you?',
            'session_id' => 'session-123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'message',
                    'session_id',
                    'conversation_id',
                    'timestamp',
                ],
            ])
            ->assertJsonPath('data.message', 'Hello, I am your assistant.');

        $this->assertDatabaseHas('rag_conversations', [
            'chatbot_id' => $this->chatbot->id,
            'session_id' => 'session-123',
        ]);

        $this->assertDatabaseHas('rag_messages', [
            'content' => 'Who are you?',
            'role' => 'user',
        ]);

        $this->assertDatabaseHas('rag_messages', [
            'content' => 'Hello, I am your assistant.',
            'role' => 'assistant',
        ]);
    }

    /** @test */
    public function test_it_returns_403_when_origin_is_not_allowed(): void
    {
        $this->chatbot->update([
            'allowed_origins' => ['https://trusted.com'],
        ]);

        $response = $this->withHeaders([
            'X-Api-Key' => $this->rawKey,
            'Origin' => 'https://malicious.com',
        ])->postJson('/ragbot/api/v1/chat', [
            'message' => 'Hello',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('type', 'UnauthorizedOriginException');
    }

    /** @test */
    public function test_it_allows_request_when_origin_matches(): void
    {
        $this->chatbot->update([
            'allowed_origins' => ['https://trusted.com'],
        ]);

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Hello']]],
            ]),
            'https://api.openai.com/v1/embeddings' => Http::response([
                'data' => [['embedding' => array_fill(0, 1536, 0.1)]],
            ]),
        ]);

        $response = $this->withHeaders([
            'X-Api-Key' => $this->rawKey,
            'Origin' => 'https://trusted.com',
        ])->postJson('/ragbot/api/v1/chat', [
            'message' => 'Hello',
        ]);

        $response->assertStatus(200)
            ->assertHeader('Access-Control-Allow-Origin', 'https://trusted.com');
    }

    /** @test */
    public function test_it_allows_all_origins_when_wildcard_is_present(): void
    {
        $this->chatbot->update([
            'allowed_origins' => ['*'],
        ]);

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Hello']]],
            ]),
            'https://api.openai.com/v1/embeddings' => Http::response([
                'data' => [['embedding' => array_fill(0, 1536, 0.1)]],
            ]),
        ]);

        $response = $this->withHeaders([
            'X-Api-Key' => $this->rawKey,
            'Origin' => 'https://any-site.com',
        ])->postJson('/ragbot/api/v1/chat', [
            'message' => 'Hello',
        ]);

        $response->assertStatus(200)
            ->assertHeader('Access-Control-Allow-Origin', 'https://any-site.com');
    }

    /** @test */
    public function test_it_returns_502_when_llm_fails(): void
    {
        // Mock Embeddings success but LLM failure
        Http::fake([
            'https://api.openai.com/v1/embeddings' => Http::response([
                'data' => [['embedding' => array_fill(0, 1536, 0.1)]],
            ]),
            'https://api.openai.com/v1/chat/completions' => Http::response(['error' => 'OpenAI Down'], 500),
        ]);

        $response = $this->withHeaders([
            'X-Api-Key' => $this->rawKey,
        ])->postJson('/ragbot/api/v1/chat', [
            'message' => 'Hello',
            'session_id' => 'session-123',
        ]);

        $response->assertStatus(502)
            ->assertJson([
                'type' => 'LlmResponseException',
            ]);
    }
}
