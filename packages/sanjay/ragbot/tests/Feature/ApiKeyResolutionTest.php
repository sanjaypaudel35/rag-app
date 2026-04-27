<?php

namespace Sanjay\Ragbot\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Models\Project;
use Tests\TestCase;

/**
 * Feature test for API key resolution and container binding.
 */
class ApiKeyResolutionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the API returns 401 when the X-Api-Key header is missing.
     */
    public function test_api_returns_401_when_api_key_is_missing(): void
    {
        $response = $this->getJson("/ragbot/api/v1/ping");

        $response->assertStatus(401)
            ->assertJson([
                "error" => "Invalid or missing API key",
            ]);
    }

    /**
     * Test that the API returns 401 when an invalid API key is provided.
     */
    public function test_api_returns_401_when_api_key_is_invalid(): void
    {
        $response = $this->withHeaders([
            "X-Api-Key" => "invalid_key",
        ])->getJson("/ragbot/api/v1/ping");

        $response->assertStatus(401)
            ->assertJson([
                "error" => "Invalid or missing API key",
            ]);
    }

    /**
     * Test that the API returns 200 and resolves the project when a valid API key is provided.
     */
    public function test_api_resolves_project_with_valid_api_key(): void
    {
        $project = Project::factory()->create([
            "name" => "Automated Test Project",
            "api_key" => "valid_test_key",
            "is_active" => true,
        ]);

        $response = $this->withHeaders([
            "X-Api-Key" => "valid_test_key",
        ])->getJson("/ragbot/api/v1/ping");

        $response->assertStatus(200)
            ->assertJson([
                "status" => "ok",
                "project" => "Automated Test Project",
            ]);

        // Verify that the project is bound to the container
        $this->assertEquals($project->id, app("ragbot.project")->id);
    }
}
