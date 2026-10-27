<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Workspaces</h2>
        @can('create', App\Models\Workspace::class)
        <a href="{{ route('workspaces.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            Create Workspace
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($workspaces as $workspace)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $workspace->name }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $workspace->description }}</p>
            <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                Owner: {{ $workspace->owner->name }}
            </div>
            <div class="flex gap-2">
                @can('update', $workspace)
                <a href="{{ route('workspaces.edit', $workspace) }}" 
                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 text-sm">
                    Edit
                </a>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
            No workspaces found.
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $workspaces->links() }}
    </div>
</div>
