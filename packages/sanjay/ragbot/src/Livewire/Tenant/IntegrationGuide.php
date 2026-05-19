<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\View\View;
use Livewire\Component;
use Sanjay\Ragbot\Models\Project;

class IntegrationGuide extends Component
{
    public Project $project;

    public string $activeTab = 'api';

    public function mount(): void
    {
        $this->project = app('ragbot.project');

        if (request()->query('tab') === 'developer') {
            $this->activeTab = 'developer';
        }
    }
    public function render(): View
    {
        return view('ragbot::livewire.tenant.integration-guide')
            ->layout('ragbot::layouts.dashboard');
    }
}
