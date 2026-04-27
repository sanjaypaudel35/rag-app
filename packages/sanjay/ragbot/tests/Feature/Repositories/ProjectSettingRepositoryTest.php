<?php

namespace Sanjay\Ragbot\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sanjay\Ragbot\Contracts\Repositories\ProjectSettingRepositoryInterface;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\ProjectSetting;
use Sanjay\Ragbot\Tests\TestCase;

class ProjectSettingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProjectSettingRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ProjectSettingRepositoryInterface::class);
    }

    /** @test */
    public function test_it_can_find_settings_by_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $settingsA = ProjectSetting::factory()->create(["project_id" => $projectA->id]);
        $settingsB = ProjectSetting::factory()->create(["project_id" => $projectB->id]);

        $this->app->instance("ragbot.project", $projectA);
        $foundA = $this->repository->findByProject();
        $this->assertNotNull($foundA);
        $this->assertEquals($settingsA->id, $foundA->id);

        $this->app->instance("ragbot.project", $projectB);
        $foundB = $this->repository->findByProject();
        $this->assertNotNull($foundB);
        $this->assertEquals($settingsB->id, $foundB->id);
    }
}
