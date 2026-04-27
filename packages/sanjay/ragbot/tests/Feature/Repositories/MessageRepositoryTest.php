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
    public function test_it_can_find_messages_by_conversation_scoped_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $convA = Conversation::factory()->create(["project_id" => $projectA->id]);
        $convB = Conversation::factory()->create(["project_id" => $projectB->id]);

        Message::factory()->count(3)->create([
            "project_id" => $projectA->id,
            "conversation_id" => $convA->id
        ]);
        Message::factory()->count(2)->create([
            "project_id" => $projectB->id,
            "conversation_id" => $convB->id
        ]);

        $this->app->instance("ragbot.project", $projectA);
        $msgsA = $this->repository->findByConversation($convA->id);
        $this->assertCount(3, $msgsA);

        $msgsAcross = $this->repository->findByConversation($convB->id);
        $this->assertCount(0, $msgsAcross);

        $this->app->instance("ragbot.project", $projectB);
        $msgsB = $this->repository->findByConversation($convB->id);
        $this->assertCount(2, $msgsB);
    }
}
