<div>
    <!-- ClickUp Style Quick Create Modal -->
    @if($isOpen)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
             wire:click="close"
             x-data
             @keydown.escape.window="$wire.close()">
            <div class="bg-white dark:bg-[#1e1f23] text-slate-900 dark:text-slate-100 rounded-2xl shadow-2xl border border-slate-200 dark:border-[#2f3036] w-full max-w-lg overflow-hidden transform transition-all"
                 wire:click.stop>
                
                <!-- Header with ClickUp gradient accent -->
                <div class="flex items-center justify-between px-6 py-4 bg-clickup-gradient text-white">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                        <h3 class="text-sm font-bold tracking-wide">New Task</h3>
                    </div>
                    <button wire:click="close" 
                            class="p-1 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Task Title *
                        </label>
                        <input type="text" 
                               id="title"
                               wire:model="title" 
                               class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-900 dark:text-white placeholder-slate-400 transition-all font-medium"
                               placeholder="What needs to be accomplished?"
                               autofocus>
                        @error('title')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Priority
                            </label>
                            <select id="priority"
                                    wire:model="priority"
                                    class="w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="urgent">🔴 Urgent</option>
                                <option value="high">🟠 High</option>
                                <option value="normal">🔵 Normal</option>
                                <option value="low">⚪ Low</option>
                            </select>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="dueDate" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Due Date
                            </label>
                            <input type="date"
                                   id="dueDate"
                                   wire:model="dueDate"
                                   class="w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Description
                        </label>
                        <textarea id="description"
                                  wire:model="description" 
                                  rows="3"
                                  class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-900 dark:text-white placeholder-slate-400 resize-none transition-all"
                                  placeholder="Add details, notes, links..."></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-[#2b2c31]">
                        <button type="button"
                                wire:click="close"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#28292e] rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
