<div class="h-full flex flex-col p-6 lg:p-8 bg-[#f7f8fa] dark:bg-[#141517] space-y-6">
    <div class="max-w-7xl mx-auto w-full space-y-6">

        <!-- Top Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-purple-600 dark:text-purple-400 mb-1">
                    <a href="{{ route('workspaces.index') }}" class="hover:underline">Administration</a>
                    <span>/</span>
                    <span class="text-slate-500 dark:text-slate-400">Global Progress & Workload</span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                    <span><svg class="w-6 h-6 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Global Progress Monitoring</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                        System Admin
                    </span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    System-wide aggregate analytics, comparative workspace metrics, and bottleneck tracking.
                </p>
            </div>
        </div>

        <!-- Global Summary Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <!-- Workspaces -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Workspaces</span>
                <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalWorkspaces }}</p>
            </div>

            <!-- Total Projects -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Projects</span>
                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $totalProjects }}</p>
            </div>

            <!-- Total Users -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Users</span>
                <p class="text-2xl font-black text-slate-800 dark:text-slate-200 mt-1">{{ $totalUsers }}</p>
            </div>

            <!-- Total Tasks -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Tasks</span>
                <p class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $totalTasks }}</p>
            </div>

            <!-- Completed Rate -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <span>Completed</span>
                    <span class="text-emerald-500">{{ $globalCompletionRate }}%</span>
                </div>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $completedTasks }}</p>
            </div>

            <!-- Overdue Tasks -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-4 shadow-2xs">
                <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Overdue Tasks</span>
                <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $overdueTasks }}</p>
            </div>
        </div>

        <!-- Comparative Workspace Progress Section -->
        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] overflow-hidden shadow-2xs space-y-4 p-5">
            <!-- Filter Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Workspace Comparison Matrix</span>
                        <span class="text-[11px] font-semibold text-slate-400">({{ count($workspaceData) }} workspaces)</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Compare activity, task volume, deadlines, and completion across all workspaces.
                    </p>
                </div>

                <div class="flex items-center gap-2.5 flex-wrap text-xs">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Filter workspaces..." 
                               class="w-44 pl-8 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                        <svg class="absolute left-2.5 top-2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <!-- Workload Filter -->
                    <select wire:model.live="filterWorkload" 
                            class="form-select text-xs bg-slate-50 dark:bg-[#242529] border border-slate-200 dark:border-[#35363b] rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="all">All Workspaces</option>
                        <option value="has_overdue">With Overdue Tasks</option>
                        <option value="high_completion">High Completion (≥80%)</option>
                    </select>
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200/70 dark:border-[#28292d]">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-[#202126] border-b border-slate-200 dark:border-[#28292d] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-5 py-3.5">Workspace</th>
                            <th class="px-4 py-3.5">Owner</th>
                            <th wire:click="sortBy('projects_count')" class="px-4 py-3.5 text-center cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center justify-center gap-1">
                                    <span>Projects</span>
                                    @if($sortField === 'projects_count')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th wire:click="sortBy('users_count')" class="px-4 py-3.5 text-center cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center justify-center gap-1">
                                    <span>Members</span>
                                    @if($sortField === 'users_count')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th wire:click="sortBy('tasks_count')" class="px-4 py-3.5 text-center cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center justify-center gap-1">
                                    <span>Total Tasks</span>
                                    @if($sortField === 'tasks_count')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th wire:click="sortBy('completed_count')" class="px-4 py-3.5 text-center cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center justify-center gap-1">
                                    <span>Completed</span>
                                    @if($sortField === 'completed_count')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th wire:click="sortBy('overdue_count')" class="px-4 py-3.5 text-center cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center justify-center gap-1">
                                    <span>Overdue</span>
                                    @if($sortField === 'overdue_count')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th wire:click="sortBy('completion_rate')" class="px-6 py-3.5 min-w-[180px] cursor-pointer hover:text-purple-600 transition-colors">
                                <span class="inline-flex items-center justify-center gap-1">
                                    <span>Completion Rate</span>
                                    @if($sortField === 'completion_rate')
                                        <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                    @endif
                                </span>
                            </th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#25262c]">
                        @forelse($workspaceData as $row)
                            @php
                                $ws = $row['workspace'];
                            @endphp
                            <tr class="hover:bg-purple-50/30 dark:hover:bg-[#202126] transition-colors">
                                <!-- Workspace Name -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-2xs">
                                            {{ strtoupper(substr($ws->name, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-900 dark:text-white">
                                            {{ $ws->name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Owner -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($ws->owner)
                                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $ws->owner->name }}</span>
                                    @else
                                        <span class="text-slate-400 italic">No Owner</span>
                                    @endif
                                </td>

                                <!-- Projects Count -->
                                <td class="px-4 py-3.5 text-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ $row['projects_count'] }}
                                </td>

                                <!-- Users Count -->
                                <td class="px-4 py-3.5 text-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ $row['users_count'] }}
                                </td>

                                <!-- Total Tasks -->
                                <td class="px-4 py-3.5 text-center font-black text-slate-900 dark:text-white">
                                    {{ $row['tasks_count'] }}
                                </td>

                                <!-- Completed -->
                                <td class="px-4 py-3.5 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $row['completed_count'] }}
                                </td>

                                <!-- Overdue -->
                                <td class="px-4 py-3.5 text-center font-bold {{ $row['overdue_count'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                    @if($row['overdue_count'] > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800">
                                            {{ $row['overdue_count'] }}
                                        </span>
                                    @else
                                        0
                                    @endif
                                </td>

                                <!-- Completion Progress Bar -->
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-slate-100 dark:bg-[#25262a] rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-purple-500 to-emerald-500 h-2 rounded-full" 
                                                 style="width: {{ $row['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold w-10 text-right {{ $row['completion_rate'] === 100 ? 'text-emerald-600' : 'text-slate-700 dark:text-slate-300' }}">
                                            {{ $row['completion_rate'] }}%
                                        </span>
                                    </div>
                                </td>

                                <!-- Open Progress Button -->
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('workspaces.progress', $ws) }}" 
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 hover:underline">
                                        View Dashboard →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-slate-400 text-xs">
                                    No workspaces matched your search criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
