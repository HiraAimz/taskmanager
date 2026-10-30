<div class="h-full flex flex-col p-6 lg:p-8 bg-[#f7f8fa] dark:bg-[#141517] space-y-6">
    <div class="max-w-7xl mx-auto w-full space-y-6">

        <!-- Top Header & Workspace Switcher -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-purple-600 dark:text-purple-400 mb-1">
                    <a href="{{ route('workspaces.index') }}" class="hover:underline flex items-center gap-1">
                        <span>Workspaces</span>
                    </a>
                    <span>/</span>
                    <span class="text-slate-500 dark:text-slate-400">Progress & Performance</span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                    <svg class="w-7 h-7 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Workspace Progress</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                        {{ $workspace->name }}
                    </span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Monitor member task performance, project progression, and deadlines across the entire workspace.
                </p>
            </div>

            <!-- Workspace Switcher & Reset Actions -->
            <div class="flex items-center gap-2.5 flex-wrap">
                @if($switchableWorkspaces->count() > 1)
                    <div class="relative">
                        <select wire:change="switchWorkspace($event.target.value)" 
                                class="form-select text-xs font-semibold bg-white dark:bg-[#1a1b1e] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-2xs">
                            @foreach($switchableWorkspaces as $ws)
                                <option value="{{ $ws->id }}" @selected($ws->id === $workspace->id)>
                                    {{ $ws->name }} {{ $ws->owner_id === auth()->id() ? '(Owner)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button wire:click="clearFilters" 
                        class="px-3 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-[#1a1b1e] border border-slate-200 dark:border-[#2f3036] hover:bg-slate-50 dark:hover:bg-[#25262a] rounded-xl transition-colors shadow-2xs flex items-center gap-1.5"
                        title="Clear all filters">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- Date Range Filter Bar (ClickUp Style) -->
        <div class="bg-white dark:bg-[#1a1b1e] border border-slate-200/80 dark:border-[#2a2b2f] rounded-2xl p-3 shadow-2xs flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] mr-1">Period:</span>
                <button wire:click="$set('dateRangeType', 'all')" 
                        class="px-2.5 py-1 rounded-lg font-semibold transition-all {{ $dateRangeType === 'all' ? 'bg-purple-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
                    All Time
                </button>
                <button wire:click="$set('dateRangeType', 'today')" 
                        class="px-2.5 py-1 rounded-lg font-semibold transition-all {{ $dateRangeType === 'today' ? 'bg-purple-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
                    Today
                </button>
                <button wire:click="$set('dateRangeType', 'this_week')" 
                        class="px-2.5 py-1 rounded-lg font-semibold transition-all {{ $dateRangeType === 'this_week' ? 'bg-purple-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
                    This Week
                </button>
                <button wire:click="$set('dateRangeType', 'this_month')" 
                        class="px-2.5 py-1 rounded-lg font-semibold transition-all {{ $dateRangeType === 'this_month' ? 'bg-purple-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#28292d]' }}">
                    This Month
                </button>
            </div>

            <!-- Custom Date Range Inputs -->
            <div class="flex items-center gap-2">
                <select wire:model.live="dateField" class="form-select text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-lg px-2 py-1 text-slate-700 dark:text-slate-300 font-medium">
                    <option value="due_date">Due Date</option>
                    <option value="completed_at">Completed Date</option>
                    <option value="created_at">Created Date</option>
                </select>
                <input type="date" wire:model.live="dateFrom" class="text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-lg px-2 py-1 text-slate-700 dark:text-slate-300">
                <span class="text-slate-400">→</span>
                <input type="date" wire:model.live="dateTo" class="text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-lg px-2 py-1 text-slate-700 dark:text-slate-300">
            </div>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <!-- Total Tasks -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                    <span>Total Tasks</span>
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $workspaceStats['total'] }}</span>
                    <span class="text-[10px] text-slate-400">{{ $projects->count() }} {{ Str::plural('project', $projects->count()) }}</span>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                    <span>Completed</span>
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $workspaceStats['completed'] }}</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        {{ $workspaceStats['completion_rate'] }}%
                    </span>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                    <span>In Progress</span>
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="mt-2">
                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $workspaceStats['in_progress'] }}</span>
                </div>
            </div>

            <!-- Pending / To Do -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                    <span>Pending</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mt-2">
                    <span class="text-2xl font-black text-slate-700 dark:text-slate-300">{{ $workspaceStats['pending'] }}</span>
                </div>
            </div>

            <!-- Overdue Tasks -->
            <div wire:click="toggleOverdueOnly" 
                 class="cursor-pointer bg-white dark:bg-[#1a1b1e] rounded-2xl border transition-all p-4 shadow-2xs flex flex-col justify-between {{ $filterOverdueOnly ? 'ring-2 ring-rose-500 border-rose-500' : 'border-slate-200/80 dark:border-[#2a2b2f] hover:border-rose-300' }}">
                <div class="flex items-center justify-between text-rose-500 text-xs font-semibold">
                    <span>Overdue</span>
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $workspaceStats['overdue'] }}</span>
                    @if($workspaceStats['overdue'] > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                            Action req.
                        </span>
                    @endif
                </div>
            </div>

            <!-- Approaching Deadlines -->
            <div wire:click="toggleUpcomingOnly" 
                 class="cursor-pointer bg-white dark:bg-[#1a1b1e] rounded-2xl border transition-all p-4 shadow-2xs flex flex-col justify-between {{ $filterUpcomingOnly ? 'ring-2 ring-amber-500 border-amber-500' : 'border-slate-200/80 dark:border-[#2a2b2f] hover:border-amber-300' }}">
                <div class="flex items-center justify-between text-amber-500 text-xs font-semibold">
                    <span>Due ≤ 3 Days</span>
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mt-2">
                    <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $workspaceStats['approaching'] }}</span>
                </div>
            </div>
        </div>

        <!-- Member Performance Tracking Section -->
        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] overflow-hidden shadow-2xs">
            <div class="p-5 border-b border-slate-100 dark:border-[#28292d] flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Member Performance &amp; Progress Tracking</span>
                        <span class="text-[11px] font-semibold text-slate-400">({{ count($memberMetrics) }} members)</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Breakdown of tasks assigned, in progress, completed, and overdue per member.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-[#202126] border-b border-slate-200/70 dark:border-[#28292d] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-5 py-3">Member</th>
                            <th class="px-4 py-3 text-center">Assigned</th>
                            <th class="px-4 py-3 text-center">Completed</th>
                            <th class="px-4 py-3 text-center">In Progress</th>
                            <th class="px-4 py-3 text-center">Pending</th>
                            <th class="px-4 py-3 text-center">Overdue</th>
                            <th class="px-6 py-3 min-w-[180px]">Completion Progress</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#25262c]">
                        @forelse($memberMetrics as $uid => $row)
                            @php
                                $m = $row['member'];
                                $isOwner = $m->id === $workspace->owner_id;
                            @endphp
                            <tr class="hover:bg-purple-50/30 dark:hover:bg-[#202126] transition-colors">
                                <!-- Member info -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-xs font-bold shadow-2xs flex-shrink-0">
                                            {{ strtoupper(substr($m->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-slate-900 dark:text-white truncate">{{ $m->name }}</span>
                                                @if($isOwner)
                                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                        Owner
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-400 truncate">{{ $m->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Total Assigned -->
                                <td class="px-4 py-3.5 text-center font-bold text-slate-800 dark:text-slate-200">
                                    {{ $row['total'] }}
                                </td>

                                <!-- Completed -->
                                <td class="px-4 py-3.5 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $row['completed'] }}
                                </td>

                                <!-- In Progress -->
                                <td class="px-4 py-3.5 text-center font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $row['in_progress'] }}
                                </td>

                                <!-- Pending -->
                                <td class="px-4 py-3.5 text-center font-medium text-slate-500 dark:text-slate-400">
                                    {{ $row['pending'] }}
                                </td>

                                <!-- Overdue -->
                                <td class="px-4 py-3.5 text-center font-bold {{ $row['overdue'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                    @if($row['overdue'] > 0)
                                        <span class="px-1.5 py-0.5 rounded-full bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800">
                                            {{ $row['overdue'] }}
                                        </span>
                                    @else
                                        0
                                    @endif
                                </td>

                                <!-- Completion Progress Bar -->
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-slate-100 dark:bg-[#25262a] rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-purple-500 to-emerald-500 h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ $row['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold w-10 text-right {{ $row['completion_rate'] === 100 ? 'text-emerald-600' : 'text-slate-700 dark:text-slate-300' }}">
                                            {{ $row['completion_rate'] }}%
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <button wire:click="inspectMember({{ $m->id }})" 
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 hover:underline">
                                        Inspect Work →
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-6 text-slate-400 text-xs">
                                    No members found in this workspace.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Workspace All-Tasks Overview Table -->
        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] overflow-hidden shadow-2xs space-y-4 p-5">
            <!-- Header & Filter Toolbar -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span><svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> All Workspace Tasks</span>
                        <span class="text-[11px] font-semibold text-slate-400">({{ $workspaceTasks->total() }} total matching)</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Unified task view across all projects in <strong class="text-slate-700 dark:text-slate-300">{{ $workspace->name }}</strong>.
                    </p>
                </div>

                <!-- Filters -->
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Search tasks..." 
                               class="w-36 sm:w-44 pl-8 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                        <svg class="absolute left-2.5 top-2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <!-- Project Filter -->
                    <select wire:model.live="selectedProjectId" 
                            class="form-select text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>

                    <!-- Member Filter -->
                    <select wire:model.live="selectedMemberId" 
                            class="form-select text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="">All Assignees</option>
                        @foreach($members as $mem)
                            <option value="{{ $mem->id }}">{{ $mem->name }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select wire:model.live="selectedStatusType" 
                            class="form-select text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="">Status: All</option>
                        <option value="todo">Pending / To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Completed / Done</option>
                    </select>

                    <!-- Priority Filter -->
                    <select wire:model.live="selectedPriority" 
                            class="form-select text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="">Priority: All</option>
                        <option value="urgent">Urgent</option>
                        <option value="high">High</option>
                        <option value="normal">Normal</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200/70 dark:border-[#28292d]">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-[#202126] border-b border-slate-200 dark:border-[#28292d] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th wire:click="sortBy('title')" class="px-5 py-3 cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center gap-1">
                                    <span>Task Title</span>
                                    @if($sortField === 'title')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th class="px-4 py-3">Project</th>
                            <th class="px-4 py-3">Assignees</th>
                            <th class="px-4 py-3">Status</th>
                            <th wire:click="sortBy('priority')" class="px-4 py-3 cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center gap-1">
                                    <span>Priority</span>
                                    @if($sortField === 'priority')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th wire:click="sortBy('due_date')" class="px-4 py-3 cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center gap-1">
                                    <span>Due Date</span>
                                    @if($sortField === 'due_date')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th class="px-4 py-3 text-right">Inspect</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#25262c]">
                        @forelse($workspaceTasks as $task)
                            @php
                                $isDone = $task->completed_at !== null || $task->status?->type === \App\Enums\StatusType::Done || $task->status?->type === 'done';
                                $isOverdue = $task->due_date && $task->due_date < $today && !$isDone;
                                $isApproaching = $task->due_date && $task->due_date >= $today && $task->due_date <= $today->copy()->addDays(3) && !$isDone;
                            @endphp
                            <tr wire:key="ws-task-{{ $task->id }}" 
                                class="hover:bg-purple-50/30 dark:hover:bg-[#202126] transition-colors cursor-pointer"
                                wire:click="selectTask({{ $task->id }})">
                                
                                <!-- Title -->
                                <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $task->status?->color ?? '#7b68ee' }}"></span>
                                        <span class="truncate max-w-md hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                            {{ $task->title }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Project Badge -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 dark:bg-[#25262a] text-slate-700 dark:text-slate-300">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $task->project->color ?? '#7b68ee' }}"></span>
                                        {{ $task->project->name }}
                                    </span>
                                </td>

                                <!-- Assignees -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center -space-x-1.5">
                                        @if($task->assignee)
                                            <div class="w-6 h-6 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-[10px] font-bold ring-2 ring-white dark:ring-[#1a1b1e]"
                                                 title="{{ $task->assignee->name }}">
                                                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        @foreach($task->assignees->where('id', '!=', $task->assignee_id)->take(2) as $collab)
                                            <div class="w-6 h-6 rounded-full bg-slate-500 flex items-center justify-center text-white text-[10px] font-bold ring-2 ring-white dark:ring-[#1a1b1e]"
                                                 title="{{ $collab->name }}">
                                                {{ strtoupper(substr($collab->name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if(!$task->assignee && $task->assignees->isEmpty())
                                            <span class="text-slate-400 text-[11px] italic">Unassigned</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status Pill -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                          style="background-color: {{ $task->status?->color ?? '#7b68ee' }}15; color: {{ $task->status?->color ?? '#7b68ee' }}">
                                        {{ $task->status?->name ?? 'None' }}
                                    </span>
                                </td>

                                <!-- Priority -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($task->priority->value === 'urgent')
                                        <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400"><svg class="w-3 h-3 inline text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 7l2.55 2.4A1 1 0 0116 11H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/></svg> Urgent</span>
                                    @elseif($task->priority->value === 'high')
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400"><svg class="w-3 h-3 inline text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 7l2.55 2.4A1 1 0 0116 11H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/></svg> High</span>
                                    @elseif($task->priority->value === 'normal')
                                        <span class="text-[10px] font-medium text-blue-600 dark:text-blue-400"><svg class="w-3 h-3 inline text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 7l2.55 2.4A1 1 0 0116 11H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/></svg> Normal</span>
                                    @else
                                        <span class="text-[10px] font-medium text-slate-400"><svg class="w-3 h-3 inline text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6" stroke-width="2"/></svg> Low</span>
                                    @endif
                                </td>

                                <!-- Due Date -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($task->due_date)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-bold' : ($isApproaching ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-500 dark:text-slate-400') }}">
                                            @if($isOverdue)<svg class="w-3.5 h-3.5 inline text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>@elseif($isApproaching)<svg class="w-3.5 h-3.5 inline text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>@endif
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">—</span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td class="px-4 py-3 text-right whitespace-nowrap" @click.stop>
                                    <button wire:click="selectTask({{ $task->id }})" 
                                            class="p-1 text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 rounded transition-colors"
                                            title="View Task Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400 text-xs">
                                    No tasks match the active filters in this workspace.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($workspaceTasks->hasPages())
                <div class="pt-2">
                    {{ $workspaceTasks->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Member Inspection Detail Modal -->
    @if($inspectingMemberData)
        @php
            $inspectedMember = $inspectingMemberData['member'];
        @endphp
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
             wire:click="closeMemberInspection">
            <div class="bg-white dark:bg-[#1e1f23] text-slate-900 dark:text-slate-100 rounded-2xl shadow-2xl border border-slate-200 dark:border-[#2f3036] w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col"
                 wire:click.stop>
                
                <!-- Modal Header -->
                <div class="p-6 border-b border-slate-200 dark:border-[#2b2c31] flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-base font-bold shadow-md">
                            {{ strtoupper(substr($inspectedMember->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $inspectedMember->name }}</h2>
                                @if($inspectedMember->id === $workspace->owner_id)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        Workspace Owner
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400">{{ $inspectedMember->email }}</p>
                        </div>
                    </div>

                    <button wire:click="closeMemberInspection" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body: Metrics & Assigned Tasks -->
                <div class="p-6 overflow-y-auto space-y-6">
                    <!-- Member Metrics Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center">
                        <div class="bg-slate-50 dark:bg-[#16171a] p-3 rounded-xl border border-slate-200/60 dark:border-[#2b2c31]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Assigned</span>
                            <p class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ $inspectingMemberData['total'] }}</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-[#16171a] p-3 rounded-xl border border-slate-200/60 dark:border-[#2b2c31]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Completed</span>
                            <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $inspectingMemberData['completed'] }}</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-[#16171a] p-3 rounded-xl border border-slate-200/60 dark:border-[#2b2c31]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">In Progress</span>
                            <p class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $inspectingMemberData['in_progress'] }}</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-[#16171a] p-3 rounded-xl border border-slate-200/60 dark:border-[#2b2c31]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Overdue</span>
                            <p class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $inspectingMemberData['overdue'] }}</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-[#16171a] p-3 rounded-xl border border-slate-200/60 dark:border-[#2b2c31]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Completion</span>
                            <p class="text-xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $inspectingMemberData['completion_rate'] }}%</p>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-500 dark:text-slate-400">Overall Completion Rate</span>
                            <span class="text-purple-600 dark:text-purple-400">{{ $inspectingMemberData['completion_rate'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-[#25262a] rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-emerald-500 h-2.5 rounded-full" 
                                 style="width: {{ $inspectingMemberData['completion_rate'] }}%"></div>
                        </div>
                    </div>

                    <!-- Member Tasks Stream -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                                Assigned Tasks in {{ $workspace->name }} ({{ $inspectingMemberTasks->count() }})
                            </h4>
                            <button wire:click="filterByMember({{ $inspectedMember->id }})" 
                                    class="text-xs text-purple-600 dark:text-purple-400 font-semibold hover:underline">
                                Filter Main Table by this Member →
                            </button>
                        </div>

                        <div class="space-y-2 max-h-72 overflow-y-auto">
                            @forelse($inspectingMemberTasks as $mtask)
                                @php
                                    $mIsDone = $mtask->completed_at !== null || $mtask->status?->type === \App\Enums\StatusType::Done || $mtask->status?->type === 'done';
                                    $mIsOverdue = $mtask->due_date && $mtask->due_date < $today && !$mIsDone;
                                @endphp
                                <div wire:click="selectTask({{ $mtask->id }})" 
                                     class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-[#16171a] border border-slate-200/70 dark:border-[#2b2c31] hover:border-purple-400 dark:hover:border-purple-500 cursor-pointer transition-colors">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $mtask->status?->color ?? '#7b68ee' }}"></span>
                                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $mtask->title }}</span>
                                        <span class="text-[10px] text-slate-400 flex-shrink-0">({{ $mtask->project->name }})</span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                              style="background-color: {{ $mtask->status?->color ?? '#7b68ee' }}20; color: {{ $mtask->status?->color ?? '#7b68ee' }}">
                                            {{ $mtask->status?->name }}
                                        </span>
                                        @if($mtask->due_date)
                                            <span class="text-[10px] font-medium {{ $mIsOverdue ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                                {{ $mtask->due_date->format('M d') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-4 text-xs text-slate-400 italic">No tasks currently assigned to this member.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 bg-slate-50 dark:bg-[#16171a] border-t border-slate-200 dark:border-[#2b2c31] flex justify-end">
                    <button wire:click="closeMemberInspection" 
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-[#28292d] rounded-xl transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Task Detail Modal -->
    @if($selectedTaskId)
        <livewire:tasks.task-detail :taskId="$selectedTaskId" :key="'task-detail-'.$selectedTaskId" @close-modal="closeTaskDetail" />
    @endif
</div>
