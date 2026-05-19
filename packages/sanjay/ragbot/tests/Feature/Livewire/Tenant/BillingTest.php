<?php

namespace Sanjay\Ragbot\Tests\Feature\Livewire\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Sanjay\Ragbot\Enums\MessageRole;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Conversation;
use Sanjay\Ragbot\Models\Message;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\RagbotUser;
use Sanjay\Ragbot\Tests\TestCase;

class BillingTest extends TestCase
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
    public function test_it_renders_billing_page_successfully(): void
    {
        Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.billing')
            ->assertStatus(200)
            ->assertSee('Billing');
    }

    /** @test */
    public function test_it_calculates_performance_data_correctly(): void
    {
        $chatbot = Chatbot::factory()->create(['project_id' => $this->project->id, 'name' => 'Support Bot']);
        $conversation = Conversation::factory()->create(['chatbot_id' => $chatbot->id, 'project_id' => $this->project->id]);

        // Message inside range
        Message::factory()->create([
            'project_id' => $this->project->id,
            'conversation_id' => $conversation->id,
            'role' => MessageRole::Assistant,
            'model' => 'gpt-4o-mini',
            'input_tokens' => 1111,
            'output_tokens' => 555,
            'created_at' => now(),
        ]);

        // Message outside range
        Message::factory()->create([
            'project_id' => $this->project->id,
            'conversation_id' => $conversation->id,
            'role' => MessageRole::Assistant,
            'model' => 'gpt-4o-mini',
            'input_tokens' => 2222,
            'output_tokens' => 999,
            'created_at' => now()->subMonths(2),
        ]);

        Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.billing')
            ->assertSet('startDate', now()->startOfMonth()->format('Y-m-d'))
            ->assertSet('endDate', now()->endOfMonth()->format('Y-m-d'))
            ->assertSee('Support Bot')
            ->assertSee('1,111') // input tokens
            ->assertSee('555')   // output tokens
            ->assertDontSee('2,222') // input tokens from old message
            ->assertDontSee('999'); // output tokens from old message
    }

    /** @test */
    public function test_it_filters_by_date_range(): void
    {
        $chatbot = Chatbot::factory()->create(['project_id' => $this->project->id, 'name' => 'Support Bot']);
        $conversation = Conversation::factory()->create(['chatbot_id' => $chatbot->id, 'project_id' => $this->project->id]);

        $pastDate = now()->subMonths(1);

        Message::factory()->create([
            'project_id' => $this->project->id,
            'conversation_id' => $conversation->id,
            'role' => MessageRole::Assistant,
            'model' => 'gpt-4o-mini',
            'input_tokens' => 5000,
            'output_tokens' => 2500,
            'created_at' => $pastDate,
        ]);

        $test = Livewire::actingAs($this->user, 'ragbot')
            ->test('ragbot.billing');

        // Initially should not see the past message
        $test->assertDontSee('5,000');

        // Set date range to include past message
        $test->set('startDate', $pastDate->copy()->startOfDay()->format('Y-m-d'))
            ->set('endDate', $pastDate->copy()->endOfDay()->format('Y-m-d'))
            ->assertSee('5,000')
            ->assertSee('2,500');
    }
}
