<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-center gap-4">
            <h2 class="text-2xl font-semibold text-gray-900">
                {{ $project->name }} - List View
            </h2>

            <div class="flex-1"></div>

            <!-- View Switcher -->
            <div class="flex gap-2">
                <a href="{{ route('projects.board', $project) }}" 
                   class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Board
                </a>
                <span class="px-3 py-2 text-sm bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg">
                    List
                </span>
                <a href="{{ route('projects.calendar', $project) }}" 
                   class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Calendar
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-4 flex flex-wrap gap-3 items-center">
            <select wire:model.live="filterStatus" 
                    class="border-gray-300 bg-white text-gray-900 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm text-sm">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterAssignee" 
                    class="border-gray-300 bg-white text-gray-900 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm text-sm">
                <option value="">All Assignees</option>
                @foreach($workspaceMembers as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterPriority" 
                    class="border-gray-300 bg-white text-gray-900 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm text-sm">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="normal">Normal</option>
                <option value="low">Low</option>
            </select>

            <button wire:click="$dispatch('open-quick-create', { statusId: {{ $project->statuses->first()->id ?? 0 }} })"
                    class="ml-auto px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all text-sm font-medium">
                + New Task
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th wire:click="sortBy('title')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Title
                                    @if($sortField === 'title')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th wire:click="sortBy('priority')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Priority
                                    @if($sortField === 'priority')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Assignee
                            </th>
                            <th wire:click="sortBy('due_date')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Due Date
                                    @if($sortField === 'due_date')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Progress
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tasks as $task)
                            <tr wire:key="task-{{ $task->id }}" 
                                wire:click="selectTask({{ $task->id }})"
                                class="hover:bg-purple-50 cursor-pointer transition-colors">
                                <!-- Title -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $task->title }}
                                            </div>
                                            @if($task->tags->isNotEmpty())
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($task->tags->take(3) as $tag)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs"
                                                              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                            {{ $tag->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $task->status->color }}"></div>
                                        <span class="text-sm text-gray-700">{{ $task->status->name }}</span>
                                    </div>
                                </td>
                                <!-- Priority -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded 
                                        @if($task->priority->value === 'urgent') bg-red-100 text-red-800
                                        @elseif($task->priority->value === 'high') bg-orange-100 text-orange-800
                                        @elseif($task->priority->value === 'normal') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($task->priority->value) }}
                                    </span>
                                </td>
                                <!-- Assignee -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($task->assignee)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 flex items-center justify-center text-white text-xs font-semibold">
                                                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $task->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">Unassigned</span>
                                    @endif
                                </td>
                                <!-- Due Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm 
                                    @if($task->due_date && $task->due_date->isPast()) text-red-600
                                    @else text-gray-700
                                    @endif">
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                                </td>
                                <!-- Progress -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($task->checklistItems->isNotEmpty())
                                        <span class="text-xs text-gray-600">
                                            ✓ {{ $task->checklistItems->where('is_completed', true)->count() }}/{{ $task->checklistItems->count() }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No tasks found. Create your first task to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $tasks->links() }}
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
