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
                        <!-- Priority (Custom ClickUp Dropdown) -->
                        <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Priority
                            </label>
                            <select id="priority" wire:model="priority" class="sr-only" tabindex="-1">
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="normal">Normal</option>
                                <option value="low">Low</option>
                            </select>

                            <button type="button" 
                                    @click="open = !open" 
                                    class="w-full flex items-center justify-between text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 hover:border-purple-400 dark:hover:border-purple-500/50 focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition-all shadow-2xs">
                                <span class="inline-flex items-center gap-2">
                                    @if($priority === 'urgent')
                                        <span class="priority-dot bg-rose-500"></span>
                                        <span>Urgent</span>
                                    @elseif($priority === 'high')
                                        <span class="priority-dot bg-amber-500"></span>
                                        <span>High</span>
                                    @elseif($priority === 'normal')
                                        <span class="priority-dot bg-blue-500"></span>
                                        <span>Normal</span>
                                    @else
                                        <span class="priority-dot bg-slate-400"></span>
                                        <span>Low</span>
                                    @endif
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute z-50 left-0 right-0 mt-1.5 bg-white dark:bg-[#1e1f23] border border-slate-200 dark:border-[#35363b] rounded-xl shadow-xl dark:shadow-2xl p-1 text-xs space-y-0.5">
                                @foreach(['urgent' => ['Urgent', 'bg-rose-500'], 'high' => ['High', 'bg-amber-500'], 'normal' => ['Normal', 'bg-blue-500'], 'low' => ['Low', 'bg-slate-400']] as $val => [$label, $dotColor])
                                    @php $isPriSel = $priority === $val; @endphp
                                    <button type="button" 
                                            @click="$wire.set('priority', '{{ $val }}'); open = false" 
                                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-left transition-colors {{ $isPriSel ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="priority-dot {{ $dotColor }}"></span>
                                            <span>{{ $label }}</span>
                                        </span>
                                        @if($isPriSel)
                                            <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
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
