<?php

namespace Sanjay\Ragbot\Tests\Feature;

use App\Models\User;
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
        $response = $this->get(config('ragbot.prefix', 'ragbot').'/health');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'package' => 'sanjay/ragbot',
        ]);
    }

    /** @test */
    public function test_it_can_browse_normal_registration_page(): void
    {
        $response = $this->get(route('ragbot.register'));

        $response->assertStatus(200);
        $response->assertViewIs('ragbot::auth.platform.register');
    }

    /** @test */
    public function test_it_can_browse_normal_login_page(): void
    {
        $response = $this->get(route('ragbot.login'));

        $response->assertStatus(200);
        $response->assertViewIs('ragbot::auth.platform.login');
    }

    /** @test */
    public function test_it_can_browse_tenant_registration_page(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);

        $response = $this->get(route('ragbot.tenant.register', ['project_slug' => 'test-project']));

        $response->assertStatus(200);
        $response->assertViewIs('ragbot::auth.tenant.register');
    }

    /** @test */
    public function test_it_can_browse_tenant_login_page(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);

        $response = $this->get(route('ragbot.tenant.login', ['project_slug' => 'test-project']));

        $response->assertStatus(200);
        $response->assertViewIs('ragbot::auth.tenant.login');
    }

    /** @test */
    public function test_it_can_register_a_tenant_user_scoped_to_a_project_but_requires_manual_login(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);

        $response = $this->post(route('ragbot.tenant.register.store', ['project_slug' => 'test-project']), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('ragbot.tenant.login', ['project_slug' => 'test-project']));
        $response->assertSessionHas('status', 'Registration successful. Please log in.');

        $this->assertDatabaseHas('ragbot_users', [
            'email' => 'john@example.com',
            'project_id' => $project->id,
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);

        $this->assertGuest('ragbot');
    }

    /** @test */
    public function test_it_can_register_a_normal_user_but_requires_manual_login(): void
    {
        $response = $this->post(route('ragbot.register.store'), [
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('ragbot.login'));
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
        ]);
        $this->assertGuest();
    }

    /** @test */
    public function test_it_cannot_login_to_tenant_with_credentials_from_another_project(): void
    {
        $projectA = Project::factory()->create(['slug' => 'project-a']);
        $projectB = Project::factory()->create(['slug' => 'project-b']);

        $userA = RagbotUser::factory()->create([
            'project_id' => $projectA->id,
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt to login to Project B with Project A's credentials
        $response = $this->post(route('ragbot.tenant.login.store', ['project_slug' => 'project-b']), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('ragbot');
    }

    /** @test */
    public function test_it_can_login_to_tenant_with_correct_credentials(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);

        $user = RagbotUser::factory()->create([
            'project_id' => $project->id,
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('ragbot.tenant.login.store', ['project_slug' => 'test-project']), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('ragbot.dashboard', ['project_slug' => 'test-project']));
        $this->assertAuthenticatedAs($user, 'ragbot');
    }

    /** @test */
    public function test_it_can_login_as_platform_user_and_is_redirected_to_platform_dashboard(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('ragbot.login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('ragbot.platform.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function test_it_can_logout_from_tenant(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);
        $user = RagbotUser::factory()->create(['project_id' => $project->id]);

        $this->actingAs($user, 'ragbot');

        $response = $this->post(route('ragbot.tenant.logout', ['project_slug' => 'test-project']));

        $response->assertRedirect(route('ragbot.tenant.login', ['project_slug' => 'test-project']));
        $this->assertGuest('ragbot');
    }

    /** @test */
    public function test_logged_in_tenant_user_is_redirected_from_login_to_dashboard(): void
    {
        $project = Project::factory()->create(['slug' => 'test-project']);
        $user = RagbotUser::factory()->create(['project_id' => $project->id]);

        $this->actingAs($user, 'ragbot');

        $response = $this->get(route('ragbot.tenant.login', ['project_slug' => 'test-project']));

        $response->assertRedirect(route('ragbot.dashboard', ['project_slug' => 'test-project']));
    }

    /** @test */
    public function test_logged_in_platform_user_is_redirected_from_login_to_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('ragbot.login'));

        $response->assertRedirect(route('ragbot.platform.dashboard'));
    }
}
