<div>
    <x-page-header
        title="Projects"
        description="Manage all your projects in one place"
    >
        @can('create', \App\Models\Project::class)
            <x-slot:actions>
                <x-button
                    tag="a"
                    href="{{ route('projects.create') }}"
                >
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </x-slot:icon>
                    New Project
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    @if($projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No projects yet</h3>
            <p class="text-gray-600 mb-4">Create your first project to get started</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="group bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg hover:border-purple-300 transition-all">
                    <div class="h-2" style="background-color: {{ $project->color ?? '#8B5CF6' }}"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">
                                    <a href="{{ route('projects.edit', $project) }}" class="hover:text-purple-600">
                                        {{ $project->name }}
                                    </a>
                                </h3>
                                <p class="text-xs text-gray-500">{{ $project->workspace->name }}</p>
                            </div>
                            @if($project->color)
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl" 
                                     style="background-color: {{ $project->color }}20">
                                    {{ $project->icon ?? '📁' }}
                                </div>
                            @endif
                        </div>
                        
                        @if($project->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $project->description }}</p>
                        @endif

                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span>{{ $project->tasks_count }} tasks</span>
                            <span>{{ $project->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="flex gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('projects.board', $project) }}" 
                               class="flex-1 text-center px-3 py-1.5 text-sm font-medium text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                                Board
                            </a>
                            <a href="{{ route('projects.list', $project) }}" 
                               class="flex-1 text-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                List
                            </a>
                            
                            @can('update', $project)
                                <a href="{{ route('projects.edit', $project) }}" 
                                   class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($projects->hasPages())
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
        @endif
    @endif
</div>
