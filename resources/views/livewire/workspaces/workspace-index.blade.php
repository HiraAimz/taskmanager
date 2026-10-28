<div>
    <x-page-header 
        title="Workspaces"
        subtitle="Organize your projects and teams"
        :breadcrumbs="[
            ['label' => 'Workspaces']
        ]"
    >
        <x-slot name="actions">
            @can('create', App\Models\Workspace::class)
                <x-button href="{{ route('workspaces.create') }}" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Workspace
                </x-button>
            @endcan
        </x-slot>
    </x-page-header>

    @if (session('success'))
        <x-alert variant="success" dismissible class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    @if($workspaces->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($workspaces as $workspace)
                <x-card class="group hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <x-avatar :name="$workspace->name" size="lg" />
                        
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <h3 class="text-card-title text-slate-900 dark:text-slate-100 mb-2">{{ $workspace->name }}</h3>
                    
                    @if($workspace->description)
                        <p class="text-body text-slate-600 dark:text-slate-400 mb-4 line-clamp-2">{{ $workspace->description }}</p>
                    @else
                        <p class="text-body text-slate-400 dark:text-slate-500 mb-4 italic">No description</p>
                    @endif
                    
                    <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-2 text-caption">
                            <x-avatar :name="$workspace->owner->name" size="xs" />
                            <span>{{ $workspace->owner->name }}</span>
                        </div>
                        <x-button href="{{ route('workspaces.edit', $workspace) }}" variant="ghost" size="sm">
                            Manage →
                        </x-button>
                    </div>
                </x-card>
            @endforeach
        </div>

        @if($workspaces->hasPages())
            <div class="mt-8">
                {{ $workspaces->links() }}
            </div>
        @endif
    @else
        <x-empty-state 
            title="No workspaces yet"
            description="Create your first workspace to organize your projects and collaborate with your team"
        >
            <x-slot name="icon">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </x-slot>
            
            @can('create', App\Models\Workspace::class)
                <x-slot name="action">
                    <x-button href="{{ route('workspaces.create') }}" variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Workspace
                    </x-button>
                </x-slot>
            @endcan
        </x-empty-state>
    @endif
</div>
