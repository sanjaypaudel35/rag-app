<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\RagbotUser;

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

    public function addTeamMember(): void
    {
        $this->validate([
            'newMember.firstname' => 'required|string|max:255',
            'newMember.lastname' => 'required|string|max:255',
            'newMember.email' => 'required|email|unique:ragbot_users,email',
            'newMember.password' => 'required|string|min:8',
        ]);

        $user = RagbotUser::create([
            'project_id' => $this->project->id,
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
        return $this->project->users()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('firstname', 'like', '%'.$this->search.'%')
                        ->orWhere('lastname', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->status !== '', function ($query) {
                if ($this->status === 'verified') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($this->status === 'pending') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('ragbot::livewire.tenant.team-manager')
            ->layout('ragbot::layouts.dashboard');
    }
}
