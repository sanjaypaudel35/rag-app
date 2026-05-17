<?php

namespace Sanjay\Ragbot\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;
use Sanjay\Ragbot\Tests\TestCase;

class WidgetControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_serves_widget_js_when_enabled()
    {
        $project = Project::factory()->create(['api_key' => hash('sha256', 'rb_p_test_key')]);
        $project->settings()->create(ProjectSetting::factory()->make([
            'widget_enabled' => true,
            'widget_title' => 'Test Widget',
        ])->toArray());

        $response = $this->get('/ragbot/api/widget.js?api_key=rb_p_test_key');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/javascript');
        $response->assertSee('Test Widget');
        $response->assertSee('ragbot-widget-container');
    }

    /** @test */
    public function test_it_returns_disabled_message_when_widget_is_disabled()
    {
        $project = Project::factory()->create(['api_key' => hash('sha256', 'rb_p_test_key')]);
        $project->settings()->create(ProjectSetting::factory()->make([
            'widget_enabled' => false,
        ])->toArray());

        $response = $this->get('/ragbot/api/widget.js?api_key=rb_p_test_key');

        $response->assertStatus(200);
        $response->assertSee('Chat widget is disabled for this project');
    }

    /** @test */
    public function test_it_returns_404_for_invalid_api_key()
    {
        $response = $this->get('/ragbot/api/widget.js?api_key=invalid_key');

        $response->assertStatus(404);
        $response->assertSee('Invalid API key');
    }

    /** @test */
    public function test_it_returns_404_when_api_key_is_missing()
    {
        $response = $this->get('/ragbot/api/widget.js');

        $response->assertStatus(404);
        $response->assertSee('Missing API key');
    }
}
