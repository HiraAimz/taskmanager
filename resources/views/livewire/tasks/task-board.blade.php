<div class="h-full flex flex-col">
    <!-- Toolbar -->
    <div class="bg-white border-b border-gray-200 px-6 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold text-gray-900">{{ $project->name }}</h2>
                <span class="text-sm text-gray-500">Board</span>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- View Switcher -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <a href="{{ route('projects.board', $project) }}" class="px-3 py-1.5 text-sm font-medium bg-white text-purple-700 rounded shadow-sm">Board</a>
                    <a href="{{ route('projects.list', $project) }}" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900">List</a>
                    <a href="{{ route('projects.calendar', $project) }}" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900">Calendar</a>
                </div>

                <!-- Filters -->
                <select wire:model.live="filterAssignee" class="text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Members</option>
                    @foreach($workspaceMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterPriority" class="text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="normal">Normal</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Board Container -->
    <div class="flex-1 overflow-x-auto overflow-y-hidden p-6">
        <div class="flex gap-4 h-full">
            @foreach($statuses as $status)
                <div class="flex-shrink-0 w-80 flex flex-col bg-gray-50 rounded-lg">
                    <!-- Column Header -->
                    <div class="flex items-center justify-between px-4 py-3 bg-white rounded-t-lg border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $status->color }}"></div>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $status->name }}</h3>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $status->tasks->count() }}</span>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Tasks Container -->
                    <div class="flex-1 overflow-y-auto p-3 space-y-2"
                         x-data="{ statusId: {{ $status->id }} }"
                         @dragover.prevent="$event.currentTarget.classList.add('bg-purple-50')"
                         @dragleave="$event.currentTarget.classList.remove('bg-purple-50')"
                         @drop.prevent="
                            $event.currentTarget.classList.remove('bg-purple-50');
                            const taskId = parseInt($event.dataTransfer.getData('text/plain'));
                            $wire.updateTaskStatus(taskId, {{ $status->id }});
                         ">
                        @forelse($status->tasks as $task)
                            <div wire:key="task-{{ $task->id }}"
                                 class="group bg-white rounded-lg p-3 shadow-sm hover:shadow-md border border-gray-200 hover:border-purple-300 cursor-move transition-all"
                                 draggable="true"
                                 @dragstart="
                                    $event.dataTransfer.effectAllowed = 'move';
                                    $event.dataTransfer.setData('text/plain', {{ $task->id }});
                                    $event.target.classList.add('opacity-50');
                                 "
                                 @dragend="$event.target.classList.remove('opacity-50')"
                                 @click="$wire.selectTask({{ $task->id }})"
                                 @click.stop>
                                
                                <!-- Task Header -->
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 leading-tight">{{ $task->title }}</h4>
                                    </div>
                                    @if($task->priority->value === 'urgent')
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>

                                <!-- Task Meta -->
                                <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                                    @if($task->due_date)
                                        <div class="flex items-center gap-1 {{ \Carbon\Carbon::parse($task->due_date)->isPast() ? 'text-red-600' : '' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                        </div>
                                    @endif

                                    @if($task->checklistItems->isNotEmpty())
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            {{ $task->checklistItems->where('is_completed', true)->count() }}/{{ $task->checklistItems->count() }}
                                        </div>
                                    @endif

                                    @if($task->comments->isNotEmpty())
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            {{ $task->comments->count() }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Tags -->
                                @if($task->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        @foreach($task->tags->take(2) as $tag)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                  style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                        @if($task->tags->count() > 2)
                                            <span class="text-xs text-gray-500">+{{ $task->tags->count() - 2 }}</span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Assignees -->
                                @if($task->assignees->isNotEmpty())
                                    <div class="flex items-center gap-1 pt-2 border-t border-gray-100">
                                        @foreach($task->assignees->take(3) as $assignee)
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold"
                                                 title="{{ $assignee->name }}">
                                                {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($task->assignees->count() > 3)
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-semibold">
                                                +{{ $task->assignees->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-8 text-sm">No tasks</p>
                        @endforelse

                        <!-- Add Task Button -->
                        <button wire:click="$dispatch('open-quick-create', { statusId: {{ $status->id }} })"
                                class="w-full py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-white rounded-lg transition-all flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
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
