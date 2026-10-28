<div class="h-full flex flex-col bg-[#f7f8fa] dark:bg-[#141517]">
    <!-- ClickUp Project Views Bar & Toolbar -->
    <div class="bg-white dark:bg-[#1a1b1e] border-b border-slate-200 dark:border-[#2a2b2f] px-6 py-3 flex-shrink-0">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Left: Project Title & ClickUp View Switcher -->
            <div class="flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full shadow-xs" style="background-color: {{ $project->color ?? '#7b68ee' }}"></span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">{{ $project->icon ? $project->icon . ' ' : '' }}{{ $project->name }}</h2>
                </div>

                <!-- ClickUp View Tabs -->
                <div class="flex items-center bg-slate-100 dark:bg-[#25262a] rounded-lg p-0.5 text-xs font-semibold">
                    <a href="{{ route('projects.list', $project) }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-white dark:bg-[#1e1f23] text-purple-600 dark:text-purple-400 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        List
                    </a>
                    <a href="{{ route('projects.board', $project) }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                        Board
                    </a>
                    <a href="{{ route('projects.calendar', $project) }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Calendar
                    </a>
                    @can('update', $project)
                    <a href="{{ route('projects.statuses', $project) }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Workflow
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Right: Search, Filters & Quick Action -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Search Input -->
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Filter tasks..." 
                           class="w-40 sm:w-52 pl-8 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-[#25262a] border border-slate-200 dark:border-[#35363b] rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                    <svg class="absolute left-2.5 top-2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Status Filter -->
                <select wire:model.live="filterStatus" 
                        class="text-xs bg-slate-50 dark:bg-[#25262a] border border-slate-200 dark:border-[#35363b] rounded-lg pl-3 pr-8 py-1.5 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                    <option value="">Status: All</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>

                <!-- Assignee Filter -->
                <select wire:model.live="filterAssignee" 
                        class="text-xs bg-slate-50 dark:bg-[#25262a] border border-slate-200 dark:border-[#35363b] rounded-lg pl-3 pr-8 py-1.5 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                    <option value="">Assignee: All</option>
                    @foreach($workspaceMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>

                <!-- Priority Filter -->
                <select wire:model.live="filterPriority" 
                        class="text-xs bg-slate-50 dark:bg-[#25262a] border border-slate-200 dark:border-[#35363b] rounded-lg pl-3 pr-8 py-1.5 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                    <option value="">Priority: All</option>
                    <option value="urgent">🔴 Urgent</option>
                    <option value="high">🟠 High</option>
                    <option value="normal">🔵 Normal</option>
                    <option value="low">⚪ Low</option>
                </select>

                <!-- Add Task Button -->
                @can('create', [\App\Models\Task::class, $project])
                <button wire:click="$dispatch('open-quick-create', { statusId: {{ $project->statuses->first()->id ?? 0 }} })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-clickup-gradient text-white text-xs font-semibold rounded-lg shadow-sm hover:opacity-95 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    New Task
                </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main List Surface -->
    <div class="flex-1 overflow-auto p-6">
        <div class="max-w-7xl mx-auto">
            
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl shadow-xs border border-slate-200/80 dark:border-[#2a2b2f] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-[#202126] border-b border-slate-200 dark:border-[#2a2b2f] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th class="w-10 px-4 py-3 text-center">#</th>
                                <th wire:click="sortBy('title')" class="px-5 py-3 cursor-pointer hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                    <div class="flex items-center gap-1.5">
                                        <span>Task Title</span>
                                        @if($sortField === 'title')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-5 py-3">Status</th>
                                <th wire:click="sortBy('priority')" class="px-5 py-3 cursor-pointer hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                    <div class="flex items-center gap-1.5">
                                        <span>Priority</span>
                                        @if($sortField === 'priority')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-5 py-3">Assignee</th>
                                <th wire:click="sortBy('due_date')" class="px-5 py-3 cursor-pointer hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                    <div class="flex items-center gap-1.5">
                                        <span>Due Date</span>
                                        @if($sortField === 'due_date')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-5 py-3">Checklist</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#25262c] text-xs">
                            @forelse($tasks as $task)
                                <tr wire:key="task-row-{{ $task->id }}" 
                                    class="group hover:bg-purple-50/40 dark:hover:bg-[#222329] transition-colors cursor-pointer"
                                    wire:click="selectTask({{ $task->id }})">
                                    
                                    <!-- Index / Status Dot -->
                                    <td class="px-4 py-3 text-center">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ $task->status->color }}"></span>
                                    </td>

                                    <!-- Title & Tags -->
                                    <td class="px-5 py-3">
                                        <div class="flex flex-col gap-1">
                                            <span class="font-semibold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                                {{ $task->title }}
                                            </span>
                                            @if($task->tags->isNotEmpty())
                                                <div class="flex items-center gap-1 flex-wrap">
                                                    @foreach($task->tags as $tag)
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium"
                                                              style="background-color: {{ $tag->color }}15; color: {{ $tag->color }}">
                                                            #{{ $tag->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Status Pill -->
                                    <td class="px-5 py-3 whitespace-nowrap" @click.stop>
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold shadow-2xs border"
                                              style="background-color: {{ $task->status->color }}15; border-color: {{ $task->status->color }}40; color: {{ $task->status->color }}">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $task->status->color }}"></span>
                                            {{ $task->status->name }}
                                        </div>
                                    </td>

                                    <!-- Priority Badge -->
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        @if($task->priority->value === 'urgent')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                🚩 Urgent
                                            </span>
                                        @elseif($task->priority->value === 'high')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                                🚩 High
                                            </span>
                                        @elseif($task->priority->value === 'normal')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                🚩 Normal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                                🏳️ Low
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Assignee -->
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        @if($task->assignee)
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-[10px] font-bold shadow-2xs">
                                                    {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                                </div>
                                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $task->assignee->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Unassigned</span>
                                        @endif
                                    </td>

                                    <!-- Due Date -->
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        @if($task->due_date)
                                            @php
                                                $isOverdue = $task->due_date->isPast() && !$task->completed_at && $task->status->type !== \App\Enums\StatusType::Done;
                                            @endphp
                                            <span class="inline-flex items-center gap-1 font-medium {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-600 dark:text-slate-300' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $task->due_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <!-- Checklist Progress -->
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        @if($task->checklistItems->isNotEmpty())
                                            @php
                                                $doneCount = $task->checklistItems->where('is_completed', true)->count();
                                                $totalCount = $task->checklistItems->count();
                                            @endphp
                                            <span class="inline-flex items-center gap-1 font-semibold {{ $doneCount === $totalCount ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400' }}">
                                                ✓ {{ $doneCount }}/{{ $totalCount }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-14 text-center">
                                        <div class="max-w-sm mx-auto space-y-3">
                                            <div class="w-12 h-12 mx-auto rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-800 dark:text-white">No tasks found</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Get started by creating a new task for this project.</p>
                                            <button wire:click="$dispatch('open-quick-create', { statusId: {{ $project->statuses->first()->id ?? 0 }} })"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-clickup-gradient text-white text-xs font-semibold rounded-lg shadow-sm">
                                                + Create First Task
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($tasks->hasPages())
                    <div class="px-6 py-3 border-t border-slate-200 dark:border-[#2a2b2f] bg-slate-50/50 dark:bg-[#1a1b1e]">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Task Detail Modal -->
    @if($selectedTaskId)
        <livewire:tasks.task-detail :taskId="$selectedTaskId" :key="'task-detail-'.$selectedTaskId" @close-modal="closeTaskDetail" />
    @endif

    <!-- Quick Create Component -->
    <livewire:tasks.task-quick-create :project="$project" />
</div>
