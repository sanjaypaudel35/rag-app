<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Sanjay\Ragbot\Models\RagbotUser;

class ProfileSettings extends Component
{
    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public function mount(): void
    {
        /** @var RagbotUser $user */
        $user = auth('ragbot')->user();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        /** @var RagbotUser $user */
        $user = auth('ragbot')->user();

        $user->update([
            'name' => $this->name,
        ]);

        session()->flash('success', 'Profile updated successfully.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password:ragbot'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var RagbotUser $user */
        $user = auth('ragbot')->user();

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('success', 'Password updated successfully.');
    }

    public function render()
    {
        return view('ragbot::livewire.tenant.profile-settings')
            ->layout('ragbot::layouts.dashboard');
    }
}
