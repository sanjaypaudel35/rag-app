<div>
    <div class="mb-8">
        <flux:heading size="xl">Profile Settings</flux:heading>
        <flux:text class="mt-1">Manage your personal information and security.</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" heading="Success" class="mb-6" closable>
            {{ session('success') }}
        </flux:callout>
    @endif

    <div class="space-y-8">
        <!-- Profile Information -->
        <flux:card>
            <div class="mb-6">
                <flux:heading size="lg">Profile Information</flux:heading>
                <flux:text size="sm">Update your account's profile information and email address.</flux:text>
            </div>

            <form wire:submit.prevent="updateProfile" class="space-y-6 max-w-xl">
                <flux:field>
                    <flux:label>Profile Photo</flux:label>
                    <div class="mt-2 flex items-center gap-6">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-full border-2 border-indigo-500 shadow-sm">
                        @elseif($current_photo)
                            <img src="{{ asset('storage/'.$current_photo) }}" class="h-20 w-20 object-cover rounded-full border border-zinc-200 dark:border-zinc-700 shadow-sm">
                        @else
                            <div class="h-20 w-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                <flux:icon icon="user" class="text-zinc-400 h-8 w-8" />
                            </div>
                        @endif

                        <div class="flex-1">
                            <flux:input type="file" wire:model="photo" />
                            <flux:description>JPG, PNG up to 512KB</flux:description>
                            <flux:error name="photo" />
                        </div>
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Display Name</flux:label>
                    <flux:input wire:model="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input wire:model="email" disabled />
                    <flux:description>Your email address cannot be changed.</flux:description>
                </flux:field>

                <div class="flex justify-start">
                    <flux:button type="submit" variant="primary">Save Profile</flux:button>
                </div>
            </form>
        </flux:card>

        <!-- Update Password -->
        <flux:card>
            <div class="mb-6">
                <flux:heading size="lg">Update Password</flux:heading>
                <flux:text size="sm">Ensure your account is using a long, random password to stay secure.</flux:text>
            </div>

            <form wire:submit.prevent="updatePassword" class="space-y-6 max-w-xl">
                <flux:field>
                    <flux:label>Current Password</flux:label>
                    <flux:input type="password" wire:model="current_password" />
                    <flux:error name="current_password" />
                </flux:field>

                <flux:field>
                    <flux:label>New Password</flux:label>
                    <flux:input type="password" wire:model="new_password" />
                    <flux:error name="new_password" />
                </flux:field>

                <flux:field>
                    <flux:label>Confirm Password</flux:label>
                    <flux:input type="password" wire:model="new_password_confirmation" />
                    <flux:error name="new_password_confirmation" />
                </flux:field>

                <div class="flex justify-start">
                    <flux:button type="submit" variant="primary">Update Password</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
