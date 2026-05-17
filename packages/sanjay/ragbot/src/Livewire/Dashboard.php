<?php

namespace Sanjay\Ragbot\Livewire;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Services\Tenant\ProjectSettingsService;

class Dashboard extends Component
{
    /**
     * The project instance.
     */
    public Project $project;

    /**
     * The project settings.
     */
    public $settings;

    /**
     * Mount the component.
     */
    public function mount(ProjectSettingsService $service): void
    {
        $this->project = app('ragbot.project');
        $this->settings = $service->getForProject($this->project);
    }

    /**
     * Get the chatbots for the project.
     */
    #[Computed]
    public function chatbots(): Collection
    {
        return $this->project->chatbots()->with('modelUsage')->latest()->get();
    }

    /**
     * Get the total cost for the project.
     */
    #[Computed]
    public function totalCost(): float
    {
        return (float) ($this->settings->total_cost ?? 0);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('ragbot::livewire.dashboard')
            ->layout('ragbot::layouts.dashboard');
    }
}
