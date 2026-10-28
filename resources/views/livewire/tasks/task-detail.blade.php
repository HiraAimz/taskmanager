<div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
     wire:click="close"
     x-data
     x-init="document.body.style.overflow = 'hidden'"
     x-destroy="document.body.style.overflow = 'auto'">
    
    <div class="bg-white dark:bg-[#1e1f23] text-slate-900 dark:text-slate-100 rounded-2xl shadow-2xl border border-slate-200 dark:border-[#2f3036] w-full max-w-5xl max-h-[92vh] overflow-hidden flex flex-col"
         wire:click.stop
         x-data
         @keydown.escape.window="$wire.close()">
        
        <!-- Header -->
        <div class="relative px-8 pt-6 pb-5 border-b border-slate-200 dark:border-[#2b2c31]">
            <div class="flex items-center justify-between mb-3">
                <!-- Breadcrumb badge -->
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ $task->project->color ?? '#7b68ee' }}"></span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $task->project->name }}</span>
                    <span>/</span>
                    <span class="font-mono text-purple-600 dark:text-purple-400">TASK-{{ $task->id }}</span>
                </div>

                <!-- Close Button -->
                <button wire:click="close" 
                        class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#28292e] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Title Input -->
            <input type="text" 
                   wire:model.blur="title"
                   class="text-2xl lg:text-3xl font-bold bg-transparent border-none focus:outline-none focus:ring-0 p-0 w-full text-slate-900 dark:text-white placeholder-slate-400"
                   placeholder="Task title...">
            @error('title')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p>
            @enderror

            <!-- Meta Timestamps -->
            <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Created {{ $task->created_at->diffForHumans() }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Updated {{ $task->updated_at->diffForHumans() }}
                </span>
                @if($task->completed_at)
                    <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-medium">
                        ✓ Completed {{ $task->completed_at->diffForHumans() }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Body Grid -->
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 lg:p-8">
                
                <!-- Main Content (2 columns) -->
                <div class="lg:col-span-2 space-y-7">
                    
                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            Description
                        </label>
                        <textarea wire:model.blur="description"
                                  rows="4"
                                  class="w-full text-xs lg:text-sm bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl p-3 focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all text-slate-800 dark:text-slate-200"
                                  placeholder="Add details, notes, or instructions for this task..."></textarea>
                    </div>

                    <!-- Checklists Section -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-[#2b2c31]">
                        <h3 class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Checklist Items
                        </h3>
                        <livewire:checklists.checklist-manager :task="$task" :key="'checklist-'.$task->id" />
                    </div>

                    <!-- Attachments Section -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-[#2b2c31]">
                        <h3 class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            Attachments & Files
                        </h3>
                        <livewire:attachments.attachment-upload :task="$task" :key="'attachments-'.$task->id" />
                    </div>

                    <!-- Comments Section -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-[#2b2c31]">
                        <h3 class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Activity & Discussion
                        </h3>
                        <livewire:comments.comment-list :task="$task" :key="'comments-'.$task->id" />
                    </div>
                </div>

                <!-- Right Sidebar Properties (ClickUp Style) -->
                <div class="space-y-5 bg-slate-50/80 dark:bg-[#16171a] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] h-fit">
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
                        <select wire:model.live="statusId"
                                class="w-full text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            @foreach($projectStatuses as $status)
                                <option value="{{ $status->id }}">● {{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Priority</label>
                        <select wire:model.live="priority"
                                class="w-full text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="urgent">🔴 Urgent</option>
                            <option value="high">🟠 High</option>
                            <option value="normal">🔵 Normal</option>
                            <option value="low">⚪ Low</option>
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Due Date</label>
                        <input type="date" 
                               wire:model.live="dueDate"
                               class="w-full text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Primary Assignee -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Primary Assignee</label>
                        <select wire:model.live="assigneeId"
                                class="w-full text-xs font-medium bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Unassigned</option>
                            @foreach($workspaceMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Collaborators (Select2-Style Searchable Multi-Select) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Collaborators
                            </label>
                            @if(count($selectedAssignees) > 0)
                                <span class="text-[10px] font-semibold text-purple-600 dark:text-purple-400">
                                    {{ count($selectedAssignees) }} selected
                                </span>
                            @endif
                        </div>
                        
                        <div x-data="{ open: false, search: '' }" 
                             class="relative"
                             @click.outside="open = false">
                            
                            <!-- Select2 Control Box / Trigger -->
                            <div @click="open = !open" 
                                 class="min-h-[38px] w-full text-xs bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-2.5 py-1.5 flex flex-wrap items-center gap-1.5 cursor-pointer hover:border-purple-400 dark:hover:border-purple-500 transition-colors focus-within:ring-2 focus-within:ring-purple-500/20 focus-within:border-purple-500">
                                
                                <!-- Selected Member Chips -->
                                @forelse($workspaceMembers->whereIn('id', $selectedAssignees) as $collab)
                                    <span class="inline-flex items-center gap-1.5 pl-1.5 pr-2 py-0.5 rounded-lg text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 shadow-2xs">
                                        <span class="w-4 h-4 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-[9px] font-bold">
                                            {{ strtoupper(substr($collab->name, 0, 1)) }}
                                        </span>
                                        <span>{{ $collab->name }}</span>
                                        <button type="button" 
                                                wire:click.stop="removeCollaborator({{ $collab->id }})" 
                                                class="hover:text-rose-500 text-purple-400 font-bold ml-0.5 transition-colors"
                                                title="Remove">
                                            ×
                                        </button>
                                    </span>
                                @empty
                                    <span class="text-slate-400 text-xs py-0.5 px-1 select-none">Click to select collaborators...</span>
                                @endforelse

                                <!-- Caret Icon -->
                                <div class="ml-auto flex items-center pr-1 text-slate-400">
                                    <svg class="w-3.5 h-3.5 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Select2 Dropdown Menu -->
                            <div x-show="open" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-50 left-0 right-0 mt-1.5 bg-white dark:bg-[#1e1f23] border border-slate-200 dark:border-[#35363b] rounded-xl shadow-xl overflow-hidden p-2 space-y-1.5">
                                
                                <!-- Search Input -->
                                <div class="relative">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" 
                                           x-model="search" 
                                           @click.stop
                                           placeholder="Type to filter users..." 
                                           class="w-full text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-lg pl-8 pr-3 py-1.5 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-purple-500">
                                </div>

                                <!-- Options List -->
                                <div class="max-h-48 overflow-y-auto space-y-0.5">
                                    @foreach($workspaceMembers as $member)
                                        @php
                                            $isSelected = in_array((int)$member->id, array_map('intval', $selectedAssignees));
                                        @endphp
                                        <div wire:key="collab-opt-{{ $member->id }}"
                                             x-show="!search.trim() || '{{ strtolower(addslashes($member->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($member->email)) }}'.includes(search.toLowerCase())"
                                             wire:click="toggleCollaborator({{ $member->id }})"
                                             class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs cursor-pointer hover:bg-slate-100 dark:hover:bg-[#28292e] transition-colors {{ $isSelected ? 'bg-purple-50/70 dark:bg-purple-950/30' : '' }}">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-[10px] font-bold shadow-2xs">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $member->name }}</span>
                                                    <span class="text-[10px] text-slate-400">{{ $member->email }}</span>
                                                </div>
                                            </div>
                                            @if($isSelected)
                                                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/60 px-1.5 py-0.5 rounded">
                                                    ✓ Selected
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-200 dark:border-[#2b2c31] px-8 py-4 bg-slate-50 dark:bg-[#18191c] flex items-center justify-between">
            <div>
                @if (session()->has('message'))
                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                        ✓ {{ session('message') }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-3">
                @can('delete', $task)
                    <button wire:click="delete"
                            wire:confirm="Are you sure you want to delete this task?"
                            class="px-4 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition-colors">
                        Delete Task
                    </button>
                @endcan

                @can('update', $task)
                <button wire:click="save"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-lg shadow-sm hover:opacity-95 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
                @endcan
            </div>
        </div>

    </div>
</div>
