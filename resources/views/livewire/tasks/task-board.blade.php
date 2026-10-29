<div class="h-full flex flex-col bg-[#f7f8fa] dark:bg-[#141517]">
    <!-- ClickUp Project Views Bar & Filter Toolbar -->
    <div class="bg-white dark:bg-[#1a1b1e] border-b border-slate-200 dark:border-[#2a2b2f] px-6 py-3 flex-shrink-0">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Left: Project Title & ClickUp View Switcher -->
            <div class="flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full shadow-xs" style="background-color: {{ $project->color ?? '#7b68ee' }}"></span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        {!! $project->renderSvgIcon('w-4 h-4 text-purple-500 shrink-0') !!}
                        <span>{{ $project->name }}</span>
                    </h2>
                </div>

                <!-- ClickUp View Tabs -->
                <div class="flex items-center bg-slate-100 dark:bg-[#25262a] rounded-lg p-0.5 text-xs font-semibold">
                    <a href="{{ route('projects.list', $project) }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        List
                    </a>
                    <a href="{{ route('projects.board', $project) }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-white dark:bg-[#1e1f23] text-purple-600 dark:text-purple-400 shadow-xs">
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

            <!-- Right: ClickUp Filters & Actions -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Assignee Filter -->
                <div class="relative">
                    <select wire:model.live="filterAssignee" 
                            class="text-xs bg-slate-50 dark:bg-[#25262a] border border-slate-200 dark:border-[#35363b] rounded-lg pl-3 pr-8 py-1.5 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                        <option value="">All Assignees</option>
                        @foreach($workspaceMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority Filter -->
                <div class="relative">
                    <select wire:model.live="filterPriority" 
                            class="text-xs bg-slate-50 dark:bg-[#25262a] border border-slate-200 dark:border-[#35363b] rounded-lg pl-3 pr-8 py-1.5 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                        <option value="">All Priorities</option>
                        <option value="urgent">● Urgent</option>
                        <option value="high">● High</option>
                        <option value="normal">● Normal</option>
                        <option value="low">○ Low</option>
                    </select>
                </div>

                <!-- Add Task Primary Button -->
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

    <!-- Board Columns Container -->
    <div class="flex-1 overflow-x-auto overflow-y-hidden p-6">
        <div class="flex gap-5 h-full items-start">
            @foreach($statuses as $status)
                <div class="flex-shrink-0 w-80 max-h-full flex flex-col bg-slate-100/80 dark:bg-[#1a1b1f] border border-slate-200/60 dark:border-[#2a2b2f] rounded-2xl shadow-xs"
                     x-data="{ isDragOver: false }">
                    
                    <!-- Column Header (ClickUp Style) -->
                    <div class="p-3.5 border-b border-slate-200/70 dark:border-[#2a2b2f] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shadow-xs" style="background-color: {{ $status->color }}"></span>
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-xs uppercase tracking-wider">{{ $status->name }}</h3>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-[#2b2c31] text-slate-600 dark:text-slate-300">
                                {{ $status->tasks->count() }}
                            </span>
                        </div>
                        
                        @can('create', [\App\Models\Task::class, $project])
                        <button wire:click="$dispatch('open-quick-create', { statusId: {{ $status->id }} })"
                                class="p-1 text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-white dark:hover:bg-[#28292e] rounded-md transition-colors"
                                title="Add task to {{ $status->name }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                        @endcan
                    </div>

                    <!-- Tasks Scroll Area with Drag & Drop -->
                    <div class="flex-1 overflow-y-auto p-3 space-y-3 transition-colors duration-150 rounded-b-2xl"
                         :class="isDragOver ? 'bg-purple-500/10 ring-2 ring-purple-500 ring-inset' : ''"
                         @dragover.prevent="isDragOver = true"
                         @dragleave="isDragOver = false"
                         @drop.prevent="
                            isDragOver = false;
                            const taskId = parseInt($event.dataTransfer.getData('text/plain'));
                            if (taskId) {
                                $wire.updateTaskStatus(taskId, {{ $status->id }});
                            }
                         ">
                        
                        @forelse($status->tasks as $task)
                            @php
                                $canChangeStatus = auth()->user()->can('changeStatus', $task);
                            @endphp
                            <div wire:key="task-card-{{ $task->id }}"
                                 class="group bg-white dark:bg-[#202126] rounded-xl p-3.5 border border-slate-200/80 dark:border-[#2f3036] hover:border-purple-400/80 dark:hover:border-purple-500/80 shadow-xs card-hover cursor-pointer"
                                 @if($canChangeStatus)
                                     draggable="true"
                                     @dragstart="
                                        $event.dataTransfer.effectAllowed = 'move';
                                        $event.dataTransfer.setData('text/plain', {{ $task->id }});
                                        $event.target.classList.add('opacity-40');
                                     "
                                     @dragend="$event.target.classList.remove('opacity-40')"
                                 @else
                                     draggable="false"
                                 @endif
                                 @click="$wire.selectTask({{ $task->id }})">
                                
                                <!-- Top Row: Priority Flag & Tags -->
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <!-- Priority Badge -->
                                    <div class="flex items-center gap-1">
                                        @if($task->priority->value === 'urgent')
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                ● Urgent
                                            </span>
                                        @elseif($task->priority->value === 'high')
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                                ● High
                                            </span>
                                        @elseif($task->priority->value === 'normal')
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                ● Normal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                                ○ Low
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Tags -->
                                    @if($task->tags->isNotEmpty())
                                        <div class="flex items-center gap-1 flex-wrap justify-end">
                                            @foreach($task->tags->take(2) as $tag)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold"
                                                      style="background-color: {{ $tag->color }}15; color: {{ $tag->color }}">
                                                    #{{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Task Title -->
                                <h4 class="text-xs font-semibold text-slate-900 dark:text-white leading-snug mb-3 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                    {{ $task->title }}
                                </h4>

                                <!-- Meta Footer: Due Date, Checklist, Comments & Assignees -->
                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-[#2b2c31] text-[11px] text-slate-500 dark:text-slate-400">
                                    
                                    <div class="flex items-center gap-2.5">
                                        <!-- Due Date -->
                                        @if($task->due_date)
                                            @php
                                                $isOverdue = $task->due_date->isPast() && !$task->completed_at && $task->status->type !== \App\Enums\StatusType::Done;
                                            @endphp
                                            <span class="flex items-center gap-1 font-medium {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-bold' : '' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $task->due_date->format('M d') }}
                                            </span>
                                        @endif

                                        <!-- Checklist -->
                                        @if($task->checklistItems->isNotEmpty())
                                            @php
                                                $doneCount = $task->checklistItems->where('is_completed', true)->count();
                                                $totalItems = $task->checklistItems->count();
                                            @endphp
                                            <span class="flex items-center gap-1 {{ $doneCount === $totalItems ? 'text-emerald-600 dark:text-emerald-400' : '' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $doneCount }}/{{ $totalItems }}
                                            </span>
                                        @endif

                                        <!-- Comments -->
                                        @if($task->comments->isNotEmpty())
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                {{ $task->comments->count() }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Overlapping Assignees -->
                                    <div class="flex items-center -space-x-1.5 overflow-hidden">
                                        @if($task->assignee)
                                            <div class="w-6 h-6 rounded-full bg-clickup-gradient border-2 border-white dark:border-[#202126] flex items-center justify-center text-white text-[10px] font-bold shadow-xs"
                                                 title="{{ $task->assignee->name }}">
                                                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                            </div>
                                        @endif

                                        @foreach($task->assignees->take(2) as $assignee)
                                            @if(!$task->assignee || $task->assignee->id !== $assignee->id)
                                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 border-2 border-white dark:border-[#202126] flex items-center justify-center text-white text-[10px] font-bold shadow-xs"
                                                     title="{{ $assignee->name }}">
                                                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 px-4 border-2 border-dashed border-slate-200 dark:border-[#2d2e35] rounded-xl">
                                <p class="text-xs text-slate-400">Drop tasks here or click below</p>
                            </div>
                        @endforelse

                        <!-- Add Task Quick Button at Column Bottom -->
                        <button wire:click="$dispatch('open-quick-create', { statusId: {{ $status->id }} })"
                                class="w-full py-2 px-3 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-white dark:hover:bg-[#202126] rounded-xl border border-transparent hover:border-slate-200 dark:hover:border-[#2f3036] transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Task
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Task Detail Modal -->
    @if($selectedTaskId)
        <livewire:tasks.task-detail :taskId="$selectedTaskId" :key="'task-detail-'.$selectedTaskId" @close-modal="closeTaskDetail" />
    @endif

    <!-- Quick Create Component -->
    <livewire:tasks.task-quick-create :project="$project" />
</div>
