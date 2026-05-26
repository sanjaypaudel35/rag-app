<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;
use Sanjay\Ragbot\Contracts\Repositories\UserRepositoryInterface;
use Sanjay\Ragbot\Models\RagbotUser;

class ProfileSettings extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public $photo;

    public ?string $current_photo = null;

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public function mount(): void
    {
        /** @var RagbotUser $user */
        $user = auth('ragbot')->user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->current_photo = $user->profile_photo_path;
    }

    public function updateProfile(UserRepositoryInterface $userRepository): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:512', // 512KB Max for small pp
        ]);

        /** @var RagbotUser $user */
        $user = auth('ragbot')->user();

        $data = [
            'name' => $this->name,
        ];

        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
            $this->current_photo = $path;
        }

        $userRepository->update($user->id, $data);

        session()->flash('success', 'Profile updated successfully.');
    }

    public function updatePassword(UserRepositoryInterface $userRepository): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password:ragbot'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var RagbotUser $user */
        $user = auth('ragbot')->user();

        $userRepository->update($user->id, [
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
