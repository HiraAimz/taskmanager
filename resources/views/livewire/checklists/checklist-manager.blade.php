<div class="space-y-3">
    <!-- Progress Bar -->
    @if($totalCount > 0)
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                <span>{{ $completedCount }} of {{ $totalCount }} completed</span>
                <span>{{ $progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    @endif

    <!-- Checklist Items -->
    <div class="space-y-2">
        @foreach($items as $item)
            <div wire:key="checklist-item-{{ $item->id }}" 
                 class="flex items-start gap-2 group">
                <!-- Checkbox -->
                <input type="checkbox" 
                       wire:click="toggleItem({{ $item->id }})"
                       {{ $item->is_completed ? 'checked' : '' }}
                       class="mt-1 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 cursor-pointer">

                <!-- Item Content -->
                <div class="flex-1">
                    @if($editingItemId === $item->id)
                        <!-- Edit Form -->
                        <div class="flex gap-2">
                            <input type="text" 
                                   wire:model="editingItemTitle"
                                   wire:keydown.enter="saveEdit"
                                   wire:keydown.escape="cancelEdit"
                                   class="flex-1 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded">
                            <button wire:click="saveEdit"
                                    class="text-xs text-green-600 dark:text-green-400 hover:underline">
                                Save
                            </button>
                            <button wire:click="cancelEdit"
                                    class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                                Cancel
                            </button>
                        </div>
                    @else
                        <!-- Display Item -->
                        <div class="flex items-start justify-between">
                            <span class="text-sm {{ $item->is_completed ? 'line-through text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $item->title }}
                            </span>
                            
                            <!-- Actions (show on hover) -->
                            <div class="hidden group-hover:flex gap-2">
                                <button wire:click="startEdit({{ $item->id }})"
                                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                    Edit
                                </button>
                                <button wire:click="deleteItem({{ $item->id }})"
                                        wire:confirm="Are you sure you want to delete this item?"
                                        class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add New Item Form -->
    <form wire:submit="addItem" class="flex gap-2 mt-3">
        <input type="text" 
               wire:model="newItemTitle"
               placeholder="Add an item..."
               class="flex-1 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm">
        <button type="submit"
                class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
            Add
        </button>
    </form>
    @error('newItemTitle')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
