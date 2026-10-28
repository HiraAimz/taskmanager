<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">
                My Tasks
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                All tasks assigned to you
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Due Today</p>
                        <p class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            {{ $dueToday->count() }}
                        </p>
                    </div>
                    <div class="text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Overdue</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $overdue->count() }}
                        </p>
                    </div>
                    <div class="text-red-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Upcoming</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $upcoming->count() }}
                        </p>
                    </div>
                    <div class="text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">No Due Date</p>
                        <p class="text-2xl font-bold text-gray-600">
                            {{ $noDueDate->count() }}
                        </p>
                    </div>
                    <div class="text-gray-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-4 border-b border-gray-200">
            <nav class="flex space-x-4">
                <button wire:click="setTab('today')"
                        class="px-3 py-2 text-sm font-medium border-b-2 transition-colors
                            {{ $activeTab === 'today' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    Due Today ({{ $dueToday->count() }})
                </button>
                <button wire:click="setTab('overdue')"
                        class="px-3 py-2 text-sm font-medium border-b-2 transition-colors
                            {{ $activeTab === 'overdue' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    Overdue ({{ $overdue->count() }})
                </button>
                <button wire:click="setTab('upcoming')"
                        class="px-3 py-2 text-sm font-medium border-b-2 transition-colors
                            {{ $activeTab === 'upcoming' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    Upcoming ({{ $upcoming->count() }})
                </button>
                <button wire:click="setTab('no-date')"
                        class="px-3 py-2 text-sm font-medium border-b-2 transition-colors
                            {{ $activeTab === 'no-date' ? 'border-gray-600 text-gray-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    No Due Date ({{ $noDueDate->count() }})
                </button>
                <button wire:click="setTab('all')"
                        class="px-3 py-2 text-sm font-medium border-b-2 transition-colors
                            {{ $activeTab === 'all' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    All Tasks ({{ $allTasks->count() }})
                </button>
            </nav>
        </div>

        <!-- Task List -->
        <div class="space-y-2">
            @php
                $tasks = match($activeTab) {
                    'today' => $dueToday,
                    'overdue' => $overdue,
                    'upcoming' => $upcoming,
                    'no-date' => $noDueDate,
                    'all' => $allTasks,
                    default => $dueToday,
                };
            @endphp

            @forelse($tasks as $task)
                <div wire:key="task-{{ $task->id }}" 
                     wire:click="selectTask({{ $task->id }})"
                     class="bg-white rounded-lg shadow border border-gray-200 p-4 hover:shadow-md hover:border-purple-200 transition-all cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <!-- Task Title -->
                            <h3 class="text-sm font-medium text-gray-900 mb-1">
                                {{ $task->title }}
                            </h3>

                            <!-- Meta Info -->
                            <div class="flex flex-wrap items-center gap-3 text-xs">
                                <!-- Project -->
                                <span class="text-gray-600">
                                    📁 {{ $task->project->name }}
                                </span>

                                <!-- Status -->
                                <div class="flex items-center gap-1">
                                    <div class="w-2 h-2 rounded-full" style="background-color: {{ $task->status->color }}"></div>
                                    <span class="text-gray-600">{{ $task->status->name }}</span>
                                </div>

                                <!-- Priority -->
                                <span class="px-2 py-0.5 rounded
                                    @if($task->priority->value === 'urgent') bg-red-100 text-red-800
                                    @elseif($task->priority->value === 'high') bg-orange-100 text-orange-800
                                    @elseif($task->priority->value === 'normal') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($task->priority->value) }}
                                </span>

                                <!-- Due Date -->
                                @if($task->due_date)
                                    <span class="
                                        @if($task->due_date->isPast()) text-red-600 font-semibold
                                        @elseif($task->due_date->isToday()) text-purple-600 font-semibold
                                        @else text-gray-600
                                        @endif">
                                        📅 {{ $task->due_date->format('M d, Y') }}
                                        @if($task->due_date->isPast())
                                            ({{ $task->due_date->diffForHumans() }})
                                        @endif
                                    </span>
                                @endif

                                <!-- Checklist Progress -->
                                @if($task->checklistItems->isNotEmpty())
                                    <span class="text-gray-600">
                                        ✓ {{ $task->checklistItems->where('is_completed', true)->count() }}/{{ $task->checklistItems->count() }}
                                    </span>
                                @endif
                            </div>

                            <!-- Tags -->
                            @if($task->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($task->tags as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs"
                                              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
                    <p class="text-gray-500">
                        No tasks in this category. Great job! 🎉
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Task Detail Modal -->
    @if($selectedTaskId)
        <livewire:tasks.task-detail :taskId="$selectedTaskId" :key="'task-detail-'.$selectedTaskId" @close-modal="closeTaskDetail" />
    @endif
</div>
