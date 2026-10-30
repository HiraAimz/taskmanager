<div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
     wire:click="close"
     x-data
     x-init="document.body.style.overflow = 'hidden'; const url = new URL(window.location.href); if (url.searchParams.has('task')) { url.searchParams.delete('task'); window.history.replaceState({}, '', url); }"
     x-destroy="document.body.style.overflow = 'auto'">
    
    <div class="bg-white dark:bg-[#1e1f23] text-slate-900 dark:text-slate-100 rounded-2xl shadow-2xl border border-slate-200 dark:border-[#2f3036] w-full max-w-5xl max-h-[92vh] overflow-hidden flex flex-col"
         wire:click.stop
         x-data="{ showDeleteConfirm: false }"
         @keydown.escape.window="if (!showDeleteConfirm) { $wire.close() }">
        
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
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Completed {{ $task->completed_at->diffForHumans() }}
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

                    <!-- Activity & Discussion Section with Tabs -->
                    <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-[#2b2c31]">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-[#2b2c31] pb-2">
                            <div class="flex items-center gap-4">
                                <button type="button" 
                                        wire:click="setTab('comments')"
                                        class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider pb-1 transition-colors {{ $activeTab === 'comments' ? 'text-purple-600 dark:text-purple-400 border-b-2 border-purple-600' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Comments ({{ $task->comments->count() }})
                                </button>
                                <button type="button" 
                                        wire:click="setTab('activity')"
                                        class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider pb-1 transition-colors {{ $activeTab === 'activity' ? 'text-purple-600 dark:text-purple-400 border-b-2 border-purple-600' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Activity History ({{ $totalActivitiesCount }})
                                </button>
                            </div>
                        </div>

                        <!-- Comments Tab Pane -->
                        <div class="{{ $activeTab === 'comments' ? 'block' : 'hidden' }}">
                            <livewire:comments.comment-list :task="$task" :key="'comments-'.$task->id" />
                        </div>

                        <!-- Activity History Tab Pane -->
                        <div class="{{ $activeTab === 'activity' ? 'block' : 'hidden' }} space-y-4">
                            @forelse($activities as $act)
                                @php
                                    $props = $act->properties ?? [];
                                    $event = $act->event ?? $props['event_type'] ?? '';
                                    $actor = $act->causer;
                                    $actorInitial = $actor ? strtoupper(substr($actor->name, 0, 1)) : '?';
                                    $oldValue = $props['old'] ?? null;
                                    $newValue = $props['new'] ?? null;
                                    $oldValue = is_array($oldValue) ? (json_encode($oldValue) ?: '') : $oldValue;
                                    $newValue = is_array($newValue) ? (json_encode($newValue) ?: '') : $newValue;
                                @endphp
                                <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50/70 dark:bg-[#16171a] border border-slate-200/70 dark:border-[#2b2c31] transition-all hover:border-slate-300 dark:hover:border-[#383940]">
                                    <!-- Actor Avatar / Event Icon -->
                                    <div class="relative shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-xs font-bold shadow-xs">
                                            {{ $actorInitial }}
                                        </div>
                                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-[9px] text-white bg-slate-700 border border-white dark:border-[#1e1f23]">
                                            @if(str_contains($event, 'completed'))
                                                <svg class="w-2.5 h-2.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @elseif(str_contains($event, 'status'))
                                                <svg class="w-2.5 h-2.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            @elseif(str_contains($event, 'assign') || str_contains($event, 'collaborator'))
                                                <svg class="w-2.5 h-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            @elseif(str_contains($event, 'priority'))
                                                <svg class="w-2.5 h-2.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                                            @elseif(str_contains($event, 'due'))
                                                <svg class="w-2.5 h-2.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @elseif(str_contains($event, 'comment'))
                                                <svg class="w-2.5 h-2.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            @elseif(str_contains($event, 'attachment'))
                                                <svg class="w-2.5 h-2.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            @else
                                                <svg class="w-2.5 h-2.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="text-xs text-slate-800 dark:text-slate-200">
                                                <span class="font-bold text-slate-900 dark:text-white">{{ $actor?->name ?? 'System' }}</span>
                                                <span class="text-slate-600 dark:text-slate-400 ml-1">{{ $act->description }}</span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 shrink-0" title="{{ $act->created_at->toDayDateTimeString() }}">
                                                {{ $act->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Value Changes Badges if old/new exist -->
                                        @if($oldValue !== null || $newValue !== null)
                                            <div class="mt-1.5 flex items-center gap-1.5 text-[11px]">
                                                @if($oldValue !== null && $oldValue !== '')
                                                    <span class="px-1.5 py-0.5 rounded bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 line-through font-medium border border-rose-200 dark:border-rose-900">
                                                        {{ $oldValue }}
                                                    </span>
                                                    <span class="text-slate-400 text-[10px]">&rarr;</span>
                                                @endif
                                                @if($newValue !== null && $newValue !== '')
                                                    <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-semibold border border-emerald-200 dark:border-emerald-900">
                                                        {{ $newValue }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Extra comment preview if available -->
                                        @if(!empty($props['comment_body']))
                                            <div class="mt-1.5 p-2 rounded-lg bg-white dark:bg-[#1a1b1f] border border-slate-200 dark:border-[#2b2c31] text-[11px] text-slate-600 dark:text-slate-300 italic">
                                                "{{ \Illuminate\Support\Str::limit($props['comment_body'], 140) }}"
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 px-4 rounded-xl border border-dashed border-slate-200 dark:border-[#2b2c31] bg-slate-50/50 dark:bg-[#16171a]/50">
                                    <svg class="w-8 h-8 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium text-slate-600 dark:text-slate-400">No activity recorded for this task yet.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Actions and changes on this task will appear here.</p>
                                </div>
                            @endforelse

                            @if($totalActivitiesCount > $activityLimit)
                                <div class="text-center pt-2">
                                    <button type="button" 
                                            wire:click="loadMoreActivity" 
                                            class="px-4 py-1.5 rounded-lg text-xs font-semibold text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/50 border border-purple-200 dark:border-purple-900 transition-colors">
                                        Load more activities ({{ $totalActivitiesCount - $activityLimit }} remaining)
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar Properties (ClickUp Style) -->
                <div class="space-y-5 bg-slate-50/80 dark:bg-[#16171a] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] h-fit">
                    
                    <!-- Status (Custom ClickUp Dropdown) -->
                    <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
                        <select wire:model.live="statusId" class="sr-only" tabindex="-1">
                            @foreach($projectStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>

                        @php
                            $currStatus = $projectStatuses->firstWhere('id', (int)$statusId);
                        @endphp
                        <button type="button" 
                                @click="open = !open" 
                                class="w-full flex items-center justify-between text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 hover:border-purple-400 dark:hover:border-purple-500/50 focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition-all shadow-2xs">
                            <span class="inline-flex items-center gap-2 truncate">
                                @if($currStatus)
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $currStatus->color }}"></span>
                                    <span class="truncate">{{ $currStatus->name }}</span>
                                @else
                                    <span class="text-slate-400">Select Status</span>
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
                            @foreach($projectStatuses as $st)
                                @php
                                    $isSel = (int)$statusId === (int)$st->id;
                                @endphp
                                <button type="button" 
                                        @click="$wire.set('statusId', {{ $st->id }}); open = false" 
                                        class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-left transition-colors {{ $isSel ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
                                    <span class="inline-flex items-center gap-2 truncate">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $st->color }}"></span>
                                        <span class="truncate">{{ $st->name }}</span>
                                    </span>
                                    @if($isSel)
                                        <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Priority (Custom ClickUp Dropdown) -->
                    <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Priority</label>
                        <select wire:model.live="priority" class="sr-only" tabindex="-1">
                            <option value="urgent">Urgent</option>
                            <option value="high">High</option>
                            <option value="normal">Normal</option>
                            <option value="low">Low</option>
                        </select>

                        <button type="button" 
                                @click="open = !open" 
                                class="w-full flex items-center justify-between text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 hover:border-purple-400 dark:hover:border-purple-500/50 focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition-all shadow-2xs">
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
                                        class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-left transition-colors {{ $isPriSel ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
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

                    <!-- Start Date -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Start Date</label>
                        <input type="date"
                               wire:model.live="startDate"
                               class="w-full text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        @error('startDate')
                            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Due Date</label>
                        <input type="date" 
                               wire:model.live="dueDate"
                               class="w-full text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        @error('dueDate')
                            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primary Assignee (Custom ClickUp Dropdown) -->
                    <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Primary Assignee</label>
                        <select wire:model.live="assigneeId" class="sr-only" tabindex="-1">
                            <option value="">Unassigned</option>
                            @foreach($workspaceMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>

                        @php
                            $currAssignee = $workspaceMembers->firstWhere('id', (int)$assigneeId);
                        @endphp
                        <button type="button" 
                                @click="open = !open" 
                                class="w-full flex items-center justify-between text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 hover:border-purple-400 dark:hover:border-purple-500/50 focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition-all shadow-2xs">
                            <span class="inline-flex items-center gap-2 truncate">
                                @if($currAssignee)
                                    <span class="w-4 h-4 rounded-full bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-300 font-bold text-[10px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($currAssignee->name, 0, 1)) }}
                                    </span>
                                    <span class="truncate">{{ $currAssignee->name }}</span>
                                @else
                                    <span class="text-slate-400 font-normal">Unassigned</span>
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
                             class="absolute z-50 left-0 right-0 mt-1.5 max-h-56 overflow-y-auto bg-white dark:bg-[#1e1f23] border border-slate-200 dark:border-[#35363b] rounded-xl shadow-xl dark:shadow-2xl p-1 text-xs space-y-0.5">
                            <button type="button" 
                                    @click="$wire.set('assigneeId', ''); open = false" 
                                    class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-left transition-colors {{ empty($assigneeId) ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
                                <span class="text-slate-500 dark:text-slate-400">Unassigned</span>
                                @if(empty($assigneeId))
                                    <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </button>
                            @foreach($workspaceMembers as $mem)
                                @php $isMemSel = (int)$assigneeId === (int)$mem->id; @endphp
                                <button type="button" 
                                        @click="$wire.set('assigneeId', '{{ $mem->id }}'); open = false" 
                                        class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-left transition-colors {{ $isMemSel ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
                                    <span class="inline-flex items-center gap-2 truncate">
                                        <span class="w-4 h-4 rounded-full bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-300 font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($mem->name, 0, 1)) }}
                                        </span>
                                        <span class="truncate">{{ $mem->name }}</span>
                                    </span>
                                    @if($isMemSel)
                                        <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
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
                                                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/60 px-1.5 py-0.5 rounded inline-flex items-center gap-0.5">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    Selected
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
        <div class="border-t border-slate-200 dark:border-[#2b2c31] px-6 py-4 bg-slate-50 dark:bg-[#18191c] flex items-center justify-between gap-4">

            <!-- Left: Status feedback -->
            <div class="shrink-0">
                @if (session()->has('message'))
                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-700 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ session('message') }}
                    </span>
                @endif
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center gap-2 ml-auto">
                @can('delete', $task)
                    <!-- Delete button — opens inline confirmation -->
                    <div class="relative" x-cloak>
                        <!-- Confirmation Panel (shown on click) -->
                        <div x-show="showDeleteConfirm"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute bottom-full right-0 mb-2 w-72 bg-white dark:bg-[#1e1f23] border border-rose-200 dark:border-rose-900/60 rounded-xl shadow-xl p-4 z-10">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">Delete this task?</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                        This will permanently remove the task, all its comments, attachments, and activity history. This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 justify-end">
                                <button type="button" 
                                        @click="showDeleteConfirm = false"
                                        class="px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#2a2b2f] rounded-lg border border-slate-200 dark:border-[#35363b] transition-colors">
                                    Cancel
                                </button>
                                <button type="button"
                                        wire:click="delete"
                                        wire:loading.attr="disabled"
                                        @click="showDeleteConfirm = false"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-colors disabled:opacity-60">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="delete">Delete</span>
                                    <span wire:loading wire:target="delete">Deleting...</span>
                                </button>
                            </div>
                        </div>

                        <!-- Delete Trigger Button -->
                        <button type="button"
                                @click="showDeleteConfirm = !showDeleteConfirm"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-lg shadow-sm transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Task
                        </button>
                    </div>
                @endcan

                @can('update', $task)
                    <button wire:click="save"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-5 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-lg shadow-sm hover:opacity-95 active:scale-95 transition-all disabled:opacity-60">
                        <svg class="w-3.5 h-3.5" wire:loading.remove wire:target="save" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg class="w-3.5 h-3.5 animate-spin" wire:loading wire:target="save" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span wire:loading.remove wire:target="save">Save Changes</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                @endcan
            </div>
        </div>

    </div>
</div>
