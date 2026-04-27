<?php

namespace Sanjay\Ragbot\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;
use Sanjay\Ragbot\Models\Conversation;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Tests\TestCase;

class ConversationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ConversationRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ConversationRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_conversation_by_session_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $convA = Conversation::factory()->create([
            'project_id' => $projectA->id,
            'session_id' => 'session-123'
        ]);
        $convB = Conversation::factory()->create([
            'project_id' => $projectB->id,
            'session_id' => 'session-123' // Same session ID but different project
        ]);

        $foundA = $this->repository->findBySession($projectA->id, 'session-123');
        $this->assertNotNull($foundA);
        $this->assertEquals($convA->id, $foundA->id);

        $foundB = $this->repository->findBySession($projectB->id, 'session-123');
        $this->assertNotNull($foundB);
        $this->assertEquals($convB->id, $foundB->id);
    }
}
