<?php

namespace Sanjay\Ragbot\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Sanjay\Ragbot\Models\Project;

class Dashboard extends Component
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
        return view('ragbot::livewire.dashboard')
            ->layout('ragbot::layouts.dashboard');
    }
}
