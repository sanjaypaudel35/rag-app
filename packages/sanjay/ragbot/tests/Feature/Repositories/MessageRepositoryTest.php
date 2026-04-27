<?php

namespace Sanjay\Ragbot\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Contracts\Repositories\MessageRepositoryInterface;
use Sanjay\Ragbot\Models\Conversation;
use Sanjay\Ragbot\Models\Message;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Tests\TestCase;

class MessageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected MessageRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(MessageRepositoryInterface::class);
    }

    /** @test */
    public function it_can_find_messages_by_conversation_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $convA = Conversation::factory()->create(['project_id' => $projectA->id]);
        $convB = Conversation::factory()->create(['project_id' => $projectB->id]);

        Message::factory()->count(3)->create([
            'project_id' => $projectA->id,
            'conversation_id' => $convA->id
        ]);
        Message::factory()->count(2)->create([
            'project_id' => $projectB->id,
            'conversation_id' => $convB->id
        ]);

        $msgsA = $this->repository->findByConversation($projectA->id, $convA->id);
        $this->assertCount(3, $msgsA);

        $msgsAcross = $this->repository->findByConversation($projectA->id, $convB->id);
        $this->assertCount(0, $msgsAcross);
    }
}
