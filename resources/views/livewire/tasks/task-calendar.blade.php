<div class="h-full flex flex-col bg-[#f7f8fa] dark:bg-[#141517]">
    <!-- ClickUp Project Views Bar & Calendar Header -->
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
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        List
                    </a>
                    <a href="{{ route('projects.board', $project) }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                        Board
                    </a>
                    <a href="{{ route('projects.calendar', $project) }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-white dark:bg-[#1e1f23] text-purple-600 dark:text-purple-400 shadow-xs">
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

            <!-- Right: Month Nav & Today Action -->
            <div class="flex items-center gap-3">
                <div class="flex items-center bg-slate-100 dark:bg-[#25262a] rounded-lg p-0.5 border border-slate-200 dark:border-[#323338]">
                    <button wire:click="previousMonth" 
                            class="px-2.5 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-md transition-colors"
                            title="Previous Month">
                        ←
                    </button>
                    <span class="px-3 py-1 text-xs font-bold text-slate-800 dark:text-white">
                        {{ $monthName }}
                    </span>
                    <button wire:click="nextMonth" 
                            class="px-2.5 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-md transition-colors"
                            title="Next Month">
                        →
                    </button>
                </div>

                <button wire:click="today" 
                        class="px-3 py-1.5 text-xs font-semibold bg-white dark:bg-[#202126] border border-slate-200 dark:border-[#323338] text-slate-700 dark:text-slate-200 rounded-lg hover:bg-slate-50 dark:hover:bg-[#28292e] shadow-2xs transition-colors">
                    Today
                </button>

                <button wire:click="$dispatch('open-quick-create', { statusId: {{ $project->statuses->first()->id ?? 0 }} })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-clickup-gradient text-white text-xs font-semibold rounded-lg shadow-sm hover:opacity-95 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    New Task
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar Grid Surface -->
    <div class="flex-1 overflow-auto p-6">
        <div class="max-w-7xl mx-auto space-y-4">
            
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl shadow-xs border border-slate-200/80 dark:border-[#2a2b2f] overflow-hidden">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 bg-slate-50/90 dark:bg-[#202126] border-b border-slate-200 dark:border-[#2a2b2f]">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="px-3 py-2.5 text-center text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                <!-- Calendar Day Cells -->
                @foreach($weeks as $week)
                    <div class="grid grid-cols-7 border-t border-slate-100 dark:border-[#25262c]">
                        @foreach($week as $day)
                            <div class="min-h-[130px] border-r border-slate-100 dark:border-[#25262c] p-2 transition-colors
                                {{ $day['isCurrentMonth'] ? 'bg-white dark:bg-[#1a1b1e]' : 'bg-slate-50/50 dark:bg-[#151619]' }}
                                {{ $day['isToday'] ? 'bg-purple-50/20 dark:bg-purple-950/20 ring-1 ring-purple-500 ring-inset' : '' }}">
                                
                                <!-- Day Number & Count -->
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-semibold w-6 h-6 flex items-center justify-center rounded-full
                                        {{ $day['isToday'] ? 'bg-clickup-gradient text-white shadow-xs' : ($day['isCurrentMonth'] ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400 dark:text-slate-600') }}">
                                        {{ $day['date']->day }}
                                    </span>
                                    
                                    @if($day['tasks']->isNotEmpty())
                                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                            {{ $day['tasks']->count() }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Tasks Pills in this Cell -->
                                <div class="space-y-1">
                                    @foreach($day['tasks']->take(3) as $task)
                                        <div wire:click="selectTask({{ $task->id }})"
                                             class="group text-[11px] p-1.5 rounded-lg cursor-pointer bg-slate-50 dark:bg-[#222328] hover:bg-white dark:hover:bg-[#28292f] border border-slate-200/80 dark:border-[#2f3036] shadow-2xs hover:shadow-xs transition-all border-l-3"
                                             style="border-left-color: {{ $task->status->color }}">
                                            <div class="font-semibold text-slate-800 dark:text-slate-200 truncate group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                                {{ $task->title }}
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-0.5 text-[10px] text-slate-400">
                                                @if($task->priority->value === 'urgent')
                                                    <span class="text-rose-500">🚩 Urgent</span>
                                                @elseif($task->priority->value === 'high')
                                                    <span class="text-amber-500">🚩 High</span>
                                                @endif
                                                @if($task->assignee)
                                                    <span class="truncate">{{ $task->assignee->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    @if($day['tasks']->count() > 3)
                                        <div class="text-[10px] font-semibold text-purple-600 dark:text-purple-400 text-center py-0.5">
                                            +{{ $day['tasks']->count() - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- Legend & Stats -->
            <div class="flex items-center gap-6 text-xs text-slate-500 dark:text-slate-400 px-2 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-clickup-gradient"></span>
                    <span class="font-medium">Today</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span>Urgent</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>High</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>Normal</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    <span>Low</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Task Detail Modal -->
    @if($selectedTaskId)
        <livewire:tasks.task-detail :taskId="$selectedTaskId" :key="'task-detail-'.$selectedTaskId" @close-modal="closeTaskDetail" />
    @endif
</div>
