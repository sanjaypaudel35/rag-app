<div>
    <div class="mb-8">
        <flux:heading size="xl">Team Management</flux:heading>
        <flux:text class="mt-1">Manage your project members and their access.</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" heading="Success" class="mb-6" closable>
            {{ session('success') }}
        </flux:callout>
    @endif

    <div class="space-y-6">
        <flux:card>
            <flux:heading size="lg" class="mb-6">Invite Team Member</flux:heading>
            <form wire:submit.prevent="addTeamMember" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <flux:field>
                    <flux:label>First Name</flux:label>
                    <flux:input wire:model="newMember.firstname" placeholder="John" />
                    <flux:error name="newMember.firstname" />
                </flux:field>
                <flux:field>
                    <flux:label>Last Name</flux:label>
                    <flux:input wire:model="newMember.lastname" placeholder="Doe" />
                    <flux:error name="newMember.lastname" />
                </flux:field>
                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input type="email" wire:model="newMember.email" placeholder="john@example.com" />
                    <flux:error name="newMember.email" />
                </flux:field>
                <flux:field>
                    <flux:label>Password</flux:label>
                    <flux:input type="password" wire:model="newMember.password" />
                    <flux:error name="newMember.password" />
                </flux:field>
                <div class="lg:col-span-4 flex justify-end mt-4">
                    <flux:button type="submit" variant="primary">Invite Member</flux:button>
                </div>
            </form>
        </flux:card>

        <flux:card class="p-0 overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Email</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Joined</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($this->teamMembers as $member)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-medium">
                                        {{ substr($member->firstname, 0, 1) }}{{ substr($member->lastname, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $member->firstname }} {{ $member->lastname }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $member->email }}</flux:table.cell>
                            <flux:table.cell>
                                @if($member->email_verified_at)
                                    <flux:badge variant="success" size="sm">Verified</flux:badge>
                                @else
                                    <flux:badge variant="warning" size="sm">Pending</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $member->created_at->diffForHumans() }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>
