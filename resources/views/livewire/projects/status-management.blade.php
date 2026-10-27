<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-semibold">Manage Statuses</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Project: {{ $project->name }}
                        </p>
                    </div>
                    <a href="{{ route('projects.edit', $project) }}" 
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        Back to Project
                    </a>
                </div>

                @if (session()->has('message'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Add New Status -->
                <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Add New Status</h3>
                    <form wire:submit="addStatus" class="flex gap-3 items-end">
                        <div class="flex-1">
                            <label for="newStatusName" class="block text-sm font-medium mb-1">
                                Status Name
                            </label>
                            <input type="text" 
                                   id="newStatusName"
                                   wire:model="newStatusName" 
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                   required>
                            @error('newStatusName')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="newStatusColor" class="block text-sm font-medium mb-1">
                                Color
                            </label>
                            <input type="color" 
                                   id="newStatusColor"
                                   wire:model="newStatusColor" 
                                   class="h-10 w-20 border-gray-300 dark:border-gray-700 rounded cursor-pointer">
                        </div>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Add Status
                        </button>
                    </form>
                </div>

                <!-- Status List -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold mb-3">Current Statuses</h3>
                    @foreach($statuses as $status)
                        <div wire:key="status-{{ $status->id }}" 
                             class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            
                            <!-- Drag Handle -->
                            <div class="text-gray-400 cursor-move">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                </svg>
                            </div>

                            <!-- Color Badge -->
                            <div class="w-4 h-4 rounded" style="background-color: {{ $status->color }}"></div>

                            <!-- Status Name (Editable) -->
                            @if($editingStatusId === $status->id)
                                <input type="text" 
                                       wire:model="editingStatusName"
                                       wire:keydown.enter="saveStatusName({{ $status->id }})"
                                       wire:keydown.escape="cancelEdit"
                                       class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm">
                            @else
                                <span class="flex-1 font-medium">{{ $status->name }}</span>
                            @endif

                            <!-- Task Count -->
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $status->tasks->count() }} tasks
                            </span>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                @if($editingStatusId === $status->id)
                                    <button wire:click="saveStatusName({{ $status->id }})" 
                                            class="text-xs text-green-600 dark:text-green-400 hover:underline">
                                        Save
                                    </button>
                                    <button wire:click="cancelEdit" 
                                            class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                                        Cancel
                                    </button>
                                @else
                                    <button wire:click="editStatusName({{ $status->id }})" 
                                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                        Rename
                                    </button>
                                @endif

                                <button wire:click="$set('editingColorId', {{ $status->id }})" 
                                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                    Recolor
                                </button>

                                <button wire:click="confirmDelete({{ $status->id }})" 
                                        class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                    Delete
                                </button>
                            </div>
                        </div>

                        @if($editingColorId === $status->id)
                            <div class="ml-12 p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <div class="flex gap-3 items-center">
                                    <input type="color" 
                                           wire:model.live="editingColor" 
                                           class="h-10 w-20 border-gray-300 dark:border-gray-700 rounded cursor-pointer">
                                    <button wire:click="saveColor({{ $status->id }})" 
                                            class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        Save Color
                                    </button>
                                    <button wire:click="$set('editingColorId', null)" 
                                            class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if($confirmingDeleteId === $status->id)
                            <div class="ml-12 p-3 bg-red-50 dark:bg-red-900/20 rounded">
                                @if($status->tasks->count() > 0)
                                    <p class="text-sm mb-3">
                                        This status has {{ $status->tasks->count() }} task(s). 
                                        Move them to another status before deleting:
                                    </p>
                                    <div class="flex gap-3 items-center">
                                        <select wire:model="moveToStatusId" 
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm">
                                            <option value="">Select a status...</option>
                                            @foreach($statuses as $s)
                                                @if($s->id !== $status->id)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button wire:click="deleteStatus({{ $status->id }})" 
                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                            Move & Delete
                                        </button>
                                        <button wire:click="$set('confirmingDeleteId', null)" 
                                                class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                            Cancel
                                        </button>
                                    </div>
                                @else
                                    <p class="text-sm mb-3">
                                        Are you sure you want to delete this status?
                                    </p>
                                    <div class="flex gap-3">
                                        <button wire:click="deleteStatus({{ $status->id }})" 
                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                        <button wire:click="$set('confirmingDeleteId', null)" 
                                                class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                            Cancel
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
