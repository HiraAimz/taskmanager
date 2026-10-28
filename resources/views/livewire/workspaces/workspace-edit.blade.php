<div>
    <x-page-header
        title="Edit Workspace"
        description="Update workspace details and manage team members"
    >
        <x-slot:breadcrumbs>
            <a href="{{ route('workspaces.index') }}" class="hover:text-foreground transition-colors">
                Workspaces
            </a>
            <span class="text-muted-foreground">/</span>
            <span class="text-muted-foreground">{{ $workspace->name }}</span>
            <span class="text-muted-foreground">/</span>
            <span class="text-muted-foreground">Edit</span>
        </x-slot:breadcrumbs>

        <x-slot:actions>
            <x-button
                variant="ghost"
                tag="a"
                href="{{ route('workspaces.index') }}"
            >
                <x-slot:icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </x-slot:icon>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if (session('success'))
        <x-alert variant="success" dismissible class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    <div class="grid gap-6 max-w-4xl">
        <!-- Workspace Details -->
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-foreground">Workspace Details</h3>
                <p class="text-sm text-muted-foreground mt-1">Basic information about this workspace</p>
            </x-slot:header>

            <form wire:submit="update" class="space-y-6">
                <x-input
                    label="Workspace Name"
                    id="name"
                    wire:model="name"
                    placeholder="e.g. Marketing Team"
                    required
                    error="{{ $errors->first('name') }}"
                >
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </x-slot:icon>
                </x-input>

                <x-textarea
                    label="Description"
                    id="description"
                    wire:model="description"
                    placeholder="Describe the purpose and goals of this workspace"
                    rows="4"
                    error="{{ $errors->first('description') }}"
                />

                <div class="flex justify-end pt-4 border-t border-border">
                    <x-button
                        type="submit"
                        loading-target="update"
                    >
                        <x-slot:icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </x-slot:icon>
                        Update Workspace
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Members Management -->
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-foreground">Team Members</h3>
                <p class="text-sm text-muted-foreground mt-1">Manage who has access to this workspace</p>
            </x-slot:header>

            <!-- Add Member Form -->
            <div class="flex gap-3 mb-6 pb-6 border-b border-border">
                <div class="flex-1">
                    <x-select
                        wire:model="selectedUserId"
                        id="selectedUserId"
                    >
                        <option value="">Select a user to add...</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </x-select>
                </div>
                <x-button
                    wire:click="addMember"
                    type="button"
                    variant="secondary"
                    loading-target="addMember"
                >
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </x-slot:icon>
                    Add Member
                </x-button>
            </div>

            <!-- Members List -->
            @if($workspace->users->count() > 0)
                <div class="overflow-hidden rounded-lg border border-border">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-muted/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Member
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-card">
                            @foreach($workspace->users as $member)
                            <tr class="hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :name="$member->name" size="sm" />
                                        <span class="text-sm font-medium text-foreground">{{ $member->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ $member->email }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($member->id === $workspace->owner_id)
                                        <x-badge variant="primary">Owner</x-badge>
                                    @else
                                        <x-badge variant="secondary">Member</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($member->id !== $workspace->owner_id)
                                        <x-button
                                            wire:click="removeMember({{ $member->id }})"
                                            wire:confirm="Are you sure you want to remove {{ $member->name }} from this workspace?"
                                            variant="ghost"
                                            size="sm"
                                            class="text-destructive hover:text-destructive hover:bg-destructive/10"
                                        >
                                            Remove
                                        </x-button>
                                    @else
                                        <span class="text-xs text-muted-foreground">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <x-empty-state
                    icon="users"
                    title="No members yet"
                    description="Add team members to collaborate on projects in this workspace"
                />
            @endif
        </x-card>
    </div>
</div>
