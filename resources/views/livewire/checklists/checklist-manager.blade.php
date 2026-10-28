<div class="space-y-3">
    <!-- Progress Bar (ClickUp Style) -->
    @if($totalCount > 0)
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#16171a] border border-slate-200/80 dark:border-[#2f3036] mb-3">
            <div class="flex justify-between items-center text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                <span>{{ $completedCount }} of {{ $totalCount }} completed</span>
                <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                <div class="bg-clickup-gradient h-1.5 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    @endif

    <!-- Checklist Items -->
    <div class="space-y-1.5">
        @foreach($items as $item)
            <div wire:key="checklist-item-{{ $item->id }}" 
                 class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-[#16171a] group transition-colors">
                
                <!-- Checkbox -->
                <input type="checkbox" 
                       wire:click="toggleItem({{ $item->id }})"
                       {{ $item->is_completed ? 'checked' : '' }}
                       class="rounded border-slate-300 dark:border-slate-600 text-purple-600 focus:ring-purple-500 cursor-pointer w-4 h-4">

                <!-- Item Content -->
                <div class="flex-1 min-w-0">
                    @if($editingItemId === $item->id)
                        <!-- Edit Form -->
                        <div class="flex gap-2">
                            <input type="text" 
                                   wire:model="editingItemTitle"
                                   wire:keydown.enter="saveEdit"
                                   wire:keydown.escape="cancelEdit"
                                   class="flex-1 text-xs bg-white dark:bg-[#202126] border border-purple-500 rounded-lg px-2.5 py-1 text-slate-800 dark:text-slate-100 focus:outline-none">
                            <button wire:click="saveEdit"
                                    class="text-xs font-bold text-emerald-600 hover:underline px-1.5">
                                Save
                            </button>
                            <button wire:click="cancelEdit"
                                    class="text-xs font-medium text-slate-400 hover:underline px-1.5">
                                Cancel
                            </button>
                        </div>
                    @else
                        <!-- Display Item -->
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium {{ $item->is_completed ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-800 dark:text-slate-200' }}">
                                {{ $item->title }}
                            </span>
                            
                            <!-- Actions (show on hover) -->
                            <div class="hidden group-hover:flex items-center gap-2">
                                <button wire:click="startEdit({{ $item->id }})"
                                        class="text-[11px] font-medium text-purple-600 hover:text-purple-700">
                                    Edit
                                </button>
                                <button wire:click="deleteItem({{ $item->id }})"
                                        wire:confirm="Delete this item?"
                                        class="text-[11px] font-medium text-rose-500 hover:text-rose-700">
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
    <form wire:submit="addItem" class="flex gap-2 mt-2">
        <input type="text" 
               wire:model="newItemTitle"
               placeholder="+ Add checklist item..."
               class="flex-1 text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-1.5 text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all font-medium">
        <button type="submit"
                class="px-3.5 py-1.5 bg-clickup-gradient text-white text-xs font-semibold rounded-xl hover:opacity-95 shadow-2xs transition-all">
            Add
        </button>
    </form>
    @error('newItemTitle')
        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
    @enderror
</div>
