<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Sanjay\Ragbot\Contracts\Repositories\UserRepositoryInterface;
use Sanjay\Ragbot\Models\Project;

class TeamManager extends Component
{
    use WithPagination;

    public Project $project;

    public $search = '';

    public $status = '';

    // Team Management
    public array $newMember = [
        'firstname' => '',
        'lastname' => '',
        'email' => '',
        'password' => '',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->project = app('ragbot.project');
    }

    public function addTeamMember(UserRepositoryInterface $userRepository): void
    {
        $this->validate([
            'newMember.firstname' => 'required|string|max:255',
            'newMember.lastname' => 'required|string|max:255',
            'newMember.email' => 'required|email|unique:ragbot_users,email',
            'newMember.password' => 'required|string|min:8',
        ]);

        $user = $userRepository->createForProject($this->project->id, [
            'firstname' => $this->newMember['firstname'],
            'lastname' => $this->newMember['lastname'],
            'name' => $this->newMember['firstname'].' '.$this->newMember['lastname'],
            'email' => $this->newMember['email'],
            'password' => Hash::make($this->newMember['password']),
        ]);

        // Trigger email verification
        event(new Registered($user));

        $this->newMember = [
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'password' => '',
        ];

        session()->flash('success', 'Team member invited successfully. A verification email has been sent.');
    }

    #[Computed]
    public function teamMembers()
    {
        return app(UserRepositoryInterface::class)->searchForProject(
            $this->project->id,
            $this->search,
            $this->status,
            10
        );
    }

    public function render(): View
    {
        return view('ragbot::livewire.tenant.team-manager')
            ->layout('ragbot::layouts.dashboard');
    }
}
