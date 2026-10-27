<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Projects</h2>
                    @can('create', \App\Models\Project::class)
                        <a href="{{ route('projects.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Create Project
                        </a>
                    @endcan
                </div>

                @if($projects->isEmpty())
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <p>No projects found. Create your first project to get started.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($projects as $project)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="text-lg font-semibold">
                                        <a href="{{ route('projects.edit', $project) }}" 
                                           class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $project->name }}
                                        </a>
                                    </h3>
                                    @if($project->color)
                                        <div class="w-4 h-4 rounded" style="background-color: {{ $project->color }}"></div>
                                    @endif
                                </div>
                                
                                @if($project->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                        {{ $project->description }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $project->tasks->count() }} tasks</span>
                                    <span>{{ $project->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="mt-3 flex gap-2">
                                    @can('update', $project)
                                        <a href="{{ route('projects.edit', $project) }}" 
                                           class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                            Edit
                                        </a>
                                    @endcan
                                    
                                    @can('delete', $project)
                                        @if($project->archived_at)
                                            <button wire:click="unarchive({{ $project->id }})" 
                                                    class="text-xs text-green-600 dark:text-green-400 hover:underline">
                                                Unarchive
                                            </button>
                                        @else
                                            <button wire:click="archive({{ $project->id }})" 
                                                    wire:confirm="Are you sure you want to archive this project?"
                                                    class="text-xs text-yellow-600 dark:text-yellow-400 hover:underline">
                                                Archive
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
