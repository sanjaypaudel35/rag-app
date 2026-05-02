<?php

namespace Sanjay\Ragbot\Tests\Feature;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\RagbotUser;
use Sanjay\Ragbot\Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    /** @test */
    public function test_it_can_access_health_check(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);

        $response = $this->get(config('ragbot.prefix', 'ragbot').'/test-project/health');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'package' => 'sanjay/ragbot',
        ]);
    }

    /** @test */
    public function test_it_can_register_a_user_scoped_to_a_project(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);

        $response = $this->post(route('ragbot.register', ['project_slug' => 'test-project']), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('ragbot.dashboard', ['project_slug' => 'test-project']));

        $this->assertDatabaseHas('ragbot_users', [
            'email' => 'john@example.com',
            'project_id' => $project->id,
        ]);
    }

    /** @test */
    public function test_it_cannot_login_with_credentials_from_another_project(): void
    {
        $projectA = Project::factory()->create(['slug' => 'project-a']);
        $projectB = Project::factory()->create(['slug' => 'project-b']);

        $userA = RagbotUser::factory()->create([
            'project_id' => $projectA->id,
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt to login to Project B with Project A's credentials
        $response = $this->post(route('ragbot.login', ['project_slug' => 'project-b']), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('ragbot');
    }

    /** @test */
    public function test_it_can_login_with_correct_credentials_for_the_project(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);

        $user = RagbotUser::factory()->create([
            'project_id' => $project->id,
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('ragbot.login', ['project_slug' => 'test-project']), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('ragbot.dashboard', ['project_slug' => 'test-project']));
        $this->assertAuthenticatedAs($user, 'ragbot');
    }

    /** @test */
    public function test_it_can_logout(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);
        $user = RagbotUser::factory()->create(['project_id' => $project->id]);

        $this->actingAs($user, 'ragbot');

        $response = $this->post(route('ragbot.logout', ['project_slug' => 'test-project']));

        $response->assertRedirect('/');
        $this->assertGuest('ragbot');
    }
}
