<div>
    <x-page-header
        title="Create Workspace"
        description="Workspaces help you organize projects and collaborate with your team"
    >
        <x-slot:breadcrumbs>
            <a href="{{ route('workspaces.index') }}" class="hover:text-foreground transition-colors">
                Workspaces
            </a>
            <span class="text-muted-foreground">/</span>
            <span class="text-muted-foreground">Create</span>
        </x-slot:breadcrumbs>
    </x-page-header>

    <div class="max-w-2xl mx-auto">
        <x-card>
            <form wire:submit="save" class="space-y-6">
                <x-form-section
                    title="Workspace Details"
                    description="Basic information about your workspace"
                >
                    <x-input
                        label="Workspace Name"
                        id="name"
                        wire:model="name"
                        placeholder="e.g. Marketing Team"
                        required
                        error="{{ $errors->first('name') }}"
                        helper="Choose a descriptive name for your workspace"
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
                        placeholder="What is this workspace for? Who will be using it?"
                        rows="5"
                        error="{{ $errors->first('description') }}"
                        helper="Add details about the purpose and goals of this workspace"
                        show-counter
                    />
                </x-form-section>

                <x-alert variant="info">
                    <strong class="font-medium">What's next?</strong>
                    <p class="mt-1">After creating your workspace, you can add team members, create projects, and start managing tasks.</p>
                </x-alert>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
                    <x-button
                        variant="ghost"
                        tag="a"
                        href="{{ route('workspaces.index') }}"
                    >
                        Cancel
                    </x-button>

                    <x-button
                        type="submit"
                        loading-target="save"
                    >
                        <x-slot:icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </x-slot:icon>
                        Create Workspace
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
