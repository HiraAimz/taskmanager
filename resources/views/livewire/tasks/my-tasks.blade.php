<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-7xl mx-auto w-full space-y-6">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span><svg class="w-6 h-6 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg> My Tasks</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Track and manage all tasks assigned to you or shared with you across workspaces.
                </p>
            </div>
        </div>

        <!-- ClickUp KPI Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- Due Today -->
            <div wire:click="setTab('today')" 
                 class="bg-white dark:bg-[#1a1b1e] rounded-2xl p-4 border transition-all cursor-pointer shadow-xs card-hover {{ $activeTab === 'today' ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-slate-200/80 dark:border-[#2a2b2f]' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Due Today</span>
                        <div class="text-2xl font-bold text-clickup-gradient mt-0.5">{{ $dueToday->count() }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Overdue -->
            <div wire:click="setTab('overdue')" 
                 class="bg-white dark:bg-[#1a1b1e] rounded-2xl p-4 border transition-all cursor-pointer shadow-xs card-hover {{ $activeTab === 'overdue' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/80 dark:border-[#2a2b2f]' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Overdue</span>
                        <div class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-0.5">{{ $overdue->count() }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Upcoming -->
            <div wire:click="setTab('upcoming')" 
                 class="bg-white dark:bg-[#1a1b1e] rounded-2xl p-4 border transition-all cursor-pointer shadow-xs card-hover {{ $activeTab === 'upcoming' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-slate-200/80 dark:border-[#2a2b2f]' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Next 7 Days</span>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-0.5">{{ $upcoming->count() }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div wire:click="setTab('completed')" 
                 class="bg-white dark:bg-[#1a1b1e] rounded-2xl p-4 border transition-all cursor-pointer shadow-xs card-hover {{ $activeTab === 'completed' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 dark:border-[#2a2b2f]' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Completed</span>
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $completed->count() }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </div>

            <!-- All My Tasks -->
            <div wire:click="setTab('all')" 
                 class="col-span-2 lg:col-span-1 bg-white dark:bg-[#1a1b1e] rounded-2xl p-4 border transition-all cursor-pointer shadow-xs card-hover {{ $activeTab === 'all' ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-slate-200/80 dark:border-[#2a2b2f]' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Assigned</span>
                        <div class="text-2xl font-bold text-slate-800 dark:text-white mt-0.5">{{ $allTasks->count() }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ClickUp Segmented Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-[#2a2b2f] pb-3 overflow-x-auto">
            <button wire:click="setTab('today')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 {{ $activeTab === 'today' ? 'bg-clickup-gradient text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#202126]' }}">
                Due Today
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'today' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $dueToday->count() }}</span>
            </button>

            <button wire:click="setTab('overdue')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 {{ $activeTab === 'overdue' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#202126]' }}">
                Overdue
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'overdue' ? 'bg-white/20 text-white' : 'bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400' }}">{{ $overdue->count() }}</span>
            </button>

            <button wire:click="setTab('upcoming')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 {{ $activeTab === 'upcoming' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#202126]' }}">
                Next 7 Days
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'upcoming' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $upcoming->count() }}</span>
            </button>

            <button wire:click="setTab('no-date')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 {{ $activeTab === 'no-date' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#202126]' }}">
                No Due Date
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'no-date' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $noDueDate->count() }}</span>
            </button>

            <button wire:click="setTab('completed')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 {{ $activeTab === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#202126]' }}">
                Completed
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'completed' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $completed->count() }}</span>
            </button>

            <button wire:click="setTab('all')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 {{ $activeTab === 'all' ? 'bg-slate-800 dark:bg-white text-white dark:text-slate-900 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#202126]' }}">
                All ({{ $allTasks->count() }})
            </button>
        </div>

        <!-- Task List Items -->
        <div class="space-y-2.5">
            @php
                $tasks = match($activeTab) {
                    'today' => $dueToday,
                    'overdue' => $overdue,
                    'upcoming' => $upcoming,
                    'no-date' => $noDueDate,
                    'completed' => $completed,
                    'all' => $allTasks,
                    default => $dueToday,
                };
            @endphp

            @forelse($tasks as $task)
                <div wire:key="my-task-{{ $task->id }}" 
                     wire:click="selectTask({{ $task->id }})"
                     class="group bg-white dark:bg-[#1a1b1e] rounded-xl p-4 border border-slate-200/80 dark:border-[#2a2b2f] hover:border-purple-400/80 dark:hover:border-purple-500/80 shadow-xs card-hover cursor-pointer transition-all">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <!-- Status Indicator -->
                            <div class="pt-0.5">
                                <span class="w-3 h-3 rounded-full inline-block shadow-2xs" style="background-color: {{ $task->status->color }}"></span>
                            </div>

                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors truncate">
                                    {{ $task->title }}
                                </h3>

                                <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                                    <!-- Project badge -->
                                    <span class="inline-flex items-center gap-1 font-medium text-slate-700 dark:text-slate-300">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $task->project->color ?? '#7b68ee' }}"></span>
                                        {{ $task->project->name }}
                                    </span>

                                    <!-- Status text -->
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                          style="background-color: {{ $task->status->color }}15; color: {{ $task->status->color }}">
                                        {{ $task->status->name }}
                                    </span>

                                    <!-- Due Date -->
                                    @if($task->due_date)
                                        @php
                                            $isOverdue = $task->due_date->isPast() && !$task->completed_at && $task->status->type !== \App\Enums\StatusType::Done;
                                        @endphp
                                        <span class="inline-flex items-center gap-1 {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-bold' : '' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $task->due_date->format('M d, Y') }}
                                            @if($isOverdue)
                                                ({{ $task->due_date->diffForHumans() }})
                                            @endif
                                        </span>
                                    @endif

                                    <!-- Checklist count -->
                                    @if($task->checklistItems->isNotEmpty())
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $task->checklistItems->where('is_completed', true)->count() }}/{{ $task->checklistItems->count() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right: Priority & Tags -->
                        <div class="flex items-center gap-2 self-start sm:self-center">
                            @if($task->priority->value === 'urgent')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                    <span class="priority-dot bg-rose-500"></span> Urgent
                                </span>
                            @elseif($task->priority->value === 'high')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                    <span class="priority-dot bg-amber-500"></span> High
                                </span>
                            @elseif($task->priority->value === 'normal')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                    <span class="priority-dot bg-blue-500"></span> Normal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                    <span class="priority-dot bg-slate-400"></span> Low
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-12 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center mb-3"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">All caught up!</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">No tasks in this category right now.</p>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Task Detail Modal -->
    @if($selectedTaskId)
        <livewire:tasks.task-detail :taskId="$selectedTaskId" :key="'task-detail-'.$selectedTaskId" @close-modal="closeTaskDetail" />
    @endif
</div>
