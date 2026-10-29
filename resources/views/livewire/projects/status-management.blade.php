<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-4xl mx-auto w-full space-y-6">
        
        <!-- Header & Breadcrumbs -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('projects.board', $project) }}" class="hover:text-purple-600 transition-colors">
                        ← Back to {{ $project->name }} Board
                    </a>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span><svg class="w-6 h-6 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7l3 3 3-3m-3-3v6"/></svg> Workflow &amp; Statuses</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Customize the stages that tasks move through in {{ $project->name }}.
                </p>
            </div>
        </div>

        <!-- Alert messages -->
        @if (session()->has('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><span>{{ session('success') }}</span></div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs font-semibold text-rose-700 dark:text-rose-300">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><span>{{ session('error') }}</span></div>
            </div>
        @endif

        <!-- Add New Status Section -->
        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-5 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3">
                Create New Status
            </h3>
            <form wire:submit="addStatus" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label for="newStatusName" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">
                        Status Name
                    </label>
                    <input type="text" 
                           id="newStatusName"
                           wire:model="newStatusName" 
                           placeholder="e.g. Under QA, Blocker, Ready for Release"
                           class="w-full text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#35363b] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium"
                           required>
                    @error('newStatusName')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="newStatusColor" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">
                        Color
                    </label>
                    <input type="color" 
                           id="newStatusColor"
                           wire:model="newStatusColor" 
                           class="h-9 w-16 bg-transparent border border-slate-200 dark:border-[#35363b] rounded-xl cursor-pointer p-0.5">
                </div>

                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Status
                </button>
            </form>
        </div>

        <!-- Statuses Ordered List -->
        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-[#25262c]">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Project Status Workflow ({{ $statuses->count() }})
                </h3>
                <span class="text-[11px] text-slate-400">Order from left to right on Board</span>
            </div>

            <div class="space-y-2.5">
                @foreach($statuses as $index => $status)
                    <div wire:key="status-item-{{ $status->id }}" 
                         class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl border border-slate-200/80 dark:border-[#28292f] bg-slate-50/50 dark:bg-[#1e1f23]">
                        
                        <!-- Left: Reorder & Name -->
                        <div class="flex items-center gap-3">
                            <!-- Reorder buttons -->
                            <div class="flex flex-col gap-0.5 text-slate-400">
                                <button type="button" 
                                        wire:click="reorderStatus({{ $status->id }}, 'up')"
                                        class="p-0.5 hover:text-purple-600 disabled:opacity-20"
                                        {{ $loop->first ? 'disabled' : '' }}
                                        title="Move up">
                                    ▲
                                </button>
                                <button type="button" 
                                        wire:click="reorderStatus({{ $status->id }}, 'down')"
                                        class="p-0.5 hover:text-purple-600 disabled:opacity-20"
                                        {{ $loop->last ? 'disabled' : '' }}
                                        title="Move down">
                                    ▼
                                </button>
                            </div>

                            <!-- Color indicator & picker -->
                            <input type="color" 
                                   value="{{ $status->color }}"
                                   wire:change="updateStatus({{ $status->id }}, 'color', $event.target.value)"
                                   class="w-6 h-6 rounded-full border-none cursor-pointer bg-transparent">

                            <div>
                                <input type="text" 
                                       value="{{ $status->name }}"
                                       wire:change="updateStatus({{ $status->id }}, 'name', $event.target.value)"
                                       class="text-xs font-bold text-slate-800 dark:text-slate-100 bg-transparent border-b border-transparent hover:border-slate-300 dark:hover:border-slate-600 focus:border-purple-500 focus:outline-none px-1 py-0.5 rounded">
                                <div class="text-[11px] text-slate-400 px-1 mt-0.5">
                                    Stage: <span class="capitalize font-medium">{{ str_replace('_', ' ', $status->type->value ?? $status->type) }}</span> • 
                                    <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $status->tasks_count }}</span> {{ Str::plural('task', $status->tasks_count) }}
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            @if($status->is_default)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                    Default
                                </span>
                            @endif

                            @can('delete', $status)
                                @if(!$status->is_default)
                                    <button type="button" 
                                            wire:click="confirmDelete({{ $status->id }})"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors"
                                            title="Delete Status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div>

                    <!-- Delete Confirmation & Move Tasks Modal Drawer -->
                    @if($deletingStatusId === $status->id)
                        <div class="ml-8 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 rounded-xl space-y-3">
                            <p class="text-xs font-semibold text-rose-800 dark:text-rose-300">
                                @if($status->tasks_count > 0)
                                    This status contains {{ $status->tasks_count }} task(s). Select another status to transfer them into before deleting:
                                @else
                                    Are you sure you want to permanently delete the "{{ $status->name }}" status?
                                @endif
                            </p>

                            @if($status->tasks_count > 0)
                                <select wire:model="moveToStatusId" 
                                        class="w-full text-xs bg-white dark:bg-[#1e1f23] border border-rose-300 dark:border-rose-800 rounded-lg px-3 py-1.5 text-slate-800 dark:text-slate-200">
                                    <option value="">-- Choose destination status --</option>
                                    @foreach($statuses as $dest)
                                        @if($dest->id !== $status->id)
                                            <option value="{{ $dest->id }}">{{ $dest->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif

                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        wire:click="deleteStatus"
                                        class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm">
                                    Confirm Delete
                                </button>
                                <button type="button" 
                                        wire:click="cancelDelete"
                                        class="px-3 py-1.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-300">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

    </div>
</div>
