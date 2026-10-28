<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-center gap-4">
            <h2 class="text-2xl font-semibold text-gray-900">
                {{ $project->name }} - Calendar View
            </h2>

            <div class="flex-1"></div>

            <!-- View Switcher -->
            <div class="flex gap-2">
                <a href="{{ route('projects.board', $project) }}" 
                   class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Board
                </a>
                <a href="{{ route('projects.list', $project) }}" 
                   class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    List
                </a>
                <span class="px-3 py-2 text-sm bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg">
                    Calendar
                </span>
            </div>
        </div>

        <!-- Calendar Controls -->
        <div class="mb-4 flex items-center justify-between bg-white p-4 rounded-lg shadow border border-gray-200">
            <button wire:click="previousMonth" 
                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                ← Previous
            </button>

            <div class="flex items-center gap-4">
                <h3 class="text-xl font-semibold text-gray-900">
                    {{ $monthName }}
                </h3>
                <button wire:click="today" 
                        class="px-3 py-1 text-sm bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all">
                    Today
                </button>
            </div>

            <button wire:click="nextMonth" 
                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Next →
            </button>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <!-- Day Headers -->
            <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="px-2 py-3 text-center text-xs font-semibold text-gray-700 uppercase">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            <!-- Calendar Days -->
            @foreach($weeks as $week)
                <div class="grid grid-cols-7 border-t border-gray-200">
                    @foreach($week as $day)
                        <div class="min-h-[120px] border-r border-gray-200 p-2 
                            {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50' }}
                            {{ $day['isToday'] ? 'ring-2 ring-purple-500 ring-inset' : '' }}">
                            
                            <!-- Date Number -->
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-medium 
                                    {{ $day['isToday'] ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full w-6 h-6 flex items-center justify-center' : '' }}
                                    {{ !$day['isCurrentMonth'] ? 'text-gray-400' : 'text-gray-700' }}">
                                    {{ $day['date']->day }}
                                </span>
                                
                                @if($day['tasks']->isNotEmpty())
                                    <span class="text-xs bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded">
                                        {{ $day['tasks']->count() }}
                                    </span>
                                @endif
                            </div>

                            <!-- Tasks for this day -->
                            <div class="space-y-1">
                                @foreach($day['tasks']->take(3) as $task)
                                    <div wire:click="selectTask({{ $task->id }})"
                                         class="text-xs p-1.5 rounded-lg cursor-pointer hover:shadow-md transition-all border-l-2 bg-white"
                                         style="border-color: {{ $task->status->color }};">
                                        <div class="font-medium text-gray-900 truncate">
                                            {{ $task->title }}
                                        </div>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <span class="text-[10px] px-1 py-0.5 rounded
                                                @if($task->priority->value === 'urgent') bg-red-100 text-red-700
                                                @elseif($task->priority->value === 'high') bg-orange-100 text-orange-700
                                                @elseif($task->priority->value === 'normal') bg-blue-100 text-blue-700
                                                @else bg-gray-100 text-gray-700
                                                @endif">
                                                {{ ucfirst($task->priority->value) }}
                                            </span>
                                            @if($task->assignee)
                                                <span class="text-[10px] text-gray-600">
                                                    {{ $task->assignee->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if($day['tasks']->count() > 3)
                                    <div class="text-[10px] text-gray-500 text-center py-1">
                                        +{{ $day['tasks']->count() - 3 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- Legend -->
        <div class="mt-4 flex gap-4 text-xs text-gray-600">
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full"></div>
                <span>Today</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 bg-red-100 rounded"></div>
                <span>Urgent</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 bg-orange-100 rounded"></div>
                <span>High</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 bg-blue-100 rounded"></div>
                <span>Normal</span>
            </div>
        </div>
    </div>

    <!-- Task Detail Modal -->
    @if($selectedTaskId)
        <livewire:tasks.task-detail :taskId="$selectedTaskId" :key="'task-detail-'.$selectedTaskId" @close-modal="closeTaskDetail" />
    @endif
</div>
