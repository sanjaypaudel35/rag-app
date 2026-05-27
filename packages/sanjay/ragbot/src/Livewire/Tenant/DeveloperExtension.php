<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\View\View;
use Livewire\Component;
use Sanjay\Ragbot\Models\Project;

class DeveloperExtension extends Component
{
    /**
     * The project instance.
     */
    public Project $project;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->project = app('ragbot.project');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('ragbot::livewire.tenant.developer-extension')
            ->layout('ragbot::layouts.dashboard');
    }
}
