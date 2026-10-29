<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-2">
            <a href="{{ route('workspaces.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Home</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-slate-200 font-medium">Settings</span>
            <span>/</span>
            <span class="text-purple-600 dark:text-purple-400 font-medium">Email Notifications</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Notification Preferences</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Manage which task activities and events trigger automatic email alerts for your account.</p>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="mb-6 flex items-center gap-2.5 p-4 text-xs font-semibold text-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300 rounded-xl border border-emerald-200 dark:border-emerald-800 transition-all shadow-2xs">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-6 flex items-center gap-2.5 p-4 text-xs font-semibold text-rose-800 bg-rose-50 dark:bg-rose-950/40 dark:text-rose-300 rounded-xl border border-rose-200 dark:border-rose-800 transition-all shadow-2xs">
            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    <!-- Preferences Settings Card -->
    <div class="bg-white dark:bg-[#1e1f23] rounded-2xl border border-slate-200 dark:border-[#2f3036] shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-[#2b2c31] bg-slate-50/60 dark:bg-[#1a1b1f] flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">Task Activity Email Notifications</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Control which updates are emailed to <span class="font-semibold text-purple-600 dark:text-purple-400">{{ auth()->user()->email }}</span>.</p>
            </div>
            <div wire:loading class="flex items-center gap-2 text-xs text-purple-600 dark:text-purple-400 font-medium">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Saving...</span>
            </div>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-[#2b2c31]">
            
            <!-- 1. Task Assigned -->
            <div class="p-6 flex items-start justify-between gap-6 hover:bg-slate-50/40 dark:hover:bg-[#232429] transition-colors">
                <div class="space-y-1 flex-1 cursor-pointer select-none" wire:click="toggle('task_assigned')">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <h3 id="pref-assigned-label" class="text-sm font-bold text-slate-900 dark:text-white">
                            Task Assignments & Collaborators
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 pl-9">
                        Notify me when I am assigned to a task, added as a collaborator, or when an assigned task is reassigned.
                    </p>
                </div>
                <button type="button" 
                        role="switch" 
                        aria-checked="{{ $task_assigned ? 'true' : 'false' }}"
                        wire:click="toggle('task_assigned')"
                        wire:loading.attr="disabled"
                        wire:target="toggle('task_assigned')"
                        aria-labelledby="pref-assigned-label"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-[#1e1f23] disabled:opacity-50 {{ $task_assigned ? 'bg-purple-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                    <span class="sr-only">Toggle notification for Task Assignments & Collaborators</span>
                    <span aria-hidden="true" 
                          class="pointer-events-none flex items-center justify-center h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $task_assigned ? 'translate-x-5' : 'translate-x-0' }}">
                        <span wire:loading wire:target="toggle('task_assigned')" class="text-purple-600">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                    </span>
                </button>
            </div>

            <!-- 2. Task Status Changed -->
            <div class="p-6 flex items-start justify-between gap-6 hover:bg-slate-50/40 dark:hover:bg-[#232429] transition-colors">
                <div class="space-y-1 flex-1 cursor-pointer select-none" wire:click="toggle('task_status_changed')">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <h3 id="pref-status-label" class="text-sm font-bold text-slate-900 dark:text-white">
                            Status Changes & Completions
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 pl-9">
                        Notify me when tasks assigned to me or created by me change status, are completed, or are reopened.
                    </p>
                </div>
                <button type="button" 
                        role="switch" 
                        aria-checked="{{ $task_status_changed ? 'true' : 'false' }}"
                        wire:click="toggle('task_status_changed')"
                        wire:loading.attr="disabled"
                        wire:target="toggle('task_status_changed')"
                        aria-labelledby="pref-status-label"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-[#1e1f23] disabled:opacity-50 {{ $task_status_changed ? 'bg-purple-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                    <span class="sr-only">Toggle notification for Status Changes & Completions</span>
                    <span aria-hidden="true" 
                          class="pointer-events-none flex items-center justify-center h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $task_status_changed ? 'translate-x-5' : 'translate-x-0' }}">
                        <span wire:loading wire:target="toggle('task_status_changed')" class="text-purple-600">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                    </span>
                </button>
            </div>

            <!-- 3. Comments & Discussion -->
            <div class="p-6 flex items-start justify-between gap-6 hover:bg-slate-50/40 dark:hover:bg-[#232429] transition-colors">
                <div class="space-y-1 flex-1 cursor-pointer select-none" wire:click="toggle('task_comment_added')">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </span>
                        <h3 id="pref-comments-label" class="text-sm font-bold text-slate-900 dark:text-white">
                            Comments & Discussion
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 pl-9">
                        Notify me when a team member posts a comment on my tasks or tasks I have created.
                    </p>
                </div>
                <button type="button" 
                        role="switch" 
                        aria-checked="{{ $task_comment_added ? 'true' : 'false' }}"
                        wire:click="toggle('task_comment_added')"
                        wire:loading.attr="disabled"
                        wire:target="toggle('task_comment_added')"
                        aria-labelledby="pref-comments-label"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-[#1e1f23] disabled:opacity-50 {{ $task_comment_added ? 'bg-purple-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                    <span class="sr-only">Toggle notification for Comments & Discussion</span>
                    <span aria-hidden="true" 
                          class="pointer-events-none flex items-center justify-center h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $task_comment_added ? 'translate-x-5' : 'translate-x-0' }}">
                        <span wire:loading wire:target="toggle('task_comment_added')" class="text-purple-600">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                    </span>
                </button>
            </div>

            <!-- 4. Due Date Changes -->
            <div class="p-6 flex items-start justify-between gap-6 hover:bg-slate-50/40 dark:hover:bg-[#232429] transition-colors">
                <div class="space-y-1 flex-1 cursor-pointer select-none" wire:click="toggle('task_due_date_changed')">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <h3 id="pref-due-label" class="text-sm font-bold text-slate-900 dark:text-white">
                            Due Date Modifications
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 pl-9">
                        Notify me when deadlines or due dates are scheduled, postponed, or modified on my tasks.
                    </p>
                </div>
                <button type="button" 
                        role="switch" 
                        aria-checked="{{ $task_due_date_changed ? 'true' : 'false' }}"
                        wire:click="toggle('task_due_date_changed')"
                        wire:loading.attr="disabled"
                        wire:target="toggle('task_due_date_changed')"
                        aria-labelledby="pref-due-label"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-[#1e1f23] disabled:opacity-50 {{ $task_due_date_changed ? 'bg-purple-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                    <span class="sr-only">Toggle notification for Due Date Modifications</span>
                    <span aria-hidden="true" 
                          class="pointer-events-none flex items-center justify-center h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $task_due_date_changed ? 'translate-x-5' : 'translate-x-0' }}">
                        <span wire:loading wire:target="toggle('task_due_date_changed')" class="text-purple-600">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                    </span>
                </button>
            </div>

            <!-- 5. Priority Changes -->
            <div class="p-6 flex items-start justify-between gap-6 hover:bg-slate-50/40 dark:hover:bg-[#232429] transition-colors">
                <div class="space-y-1 flex-1 cursor-pointer select-none" wire:click="toggle('task_priority_changed')">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        </span>
                        <h3 id="pref-priority-label" class="text-sm font-bold text-slate-900 dark:text-white">
                            Priority Level Changes
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 pl-9">
                        Notify me when the urgency or priority level of my tasks is changed (e.g. Urgent, High, Normal, Low).
                    </p>
                </div>
                <button type="button" 
                        role="switch" 
                        aria-checked="{{ $task_priority_changed ? 'true' : 'false' }}"
                        wire:click="toggle('task_priority_changed')"
                        wire:loading.attr="disabled"
                        wire:target="toggle('task_priority_changed')"
                        aria-labelledby="pref-priority-label"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-[#1e1f23] disabled:opacity-50 {{ $task_priority_changed ? 'bg-purple-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                    <span class="sr-only">Toggle notification for Priority Level Changes</span>
                    <span aria-hidden="true" 
                          class="pointer-events-none flex items-center justify-center h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $task_priority_changed ? 'translate-x-5' : 'translate-x-0' }}">
                        <span wire:loading wire:target="toggle('task_priority_changed')" class="text-purple-600">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                    </span>
                </button>
            </div>

            <!-- 6. General Updates & Attachments -->
            <div class="p-6 flex items-start justify-between gap-6 hover:bg-slate-50/40 dark:hover:bg-[#232429] transition-colors">
                <div class="space-y-1 flex-1 cursor-pointer select-none" wire:click="toggle('task_updated')">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </span>
                        <h3 id="pref-updated-label" class="text-sm font-bold text-slate-900 dark:text-white">
                            General Details & Attachments
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 pl-9">
                        Notify me when task titles, descriptions, or file attachments are updated on tasks assigned to me.
                    </p>
                </div>
                <button type="button" 
                        role="switch" 
                        aria-checked="{{ $task_updated ? 'true' : 'false' }}"
                        wire:click="toggle('task_updated')"
                        wire:loading.attr="disabled"
                        wire:target="toggle('task_updated')"
                        aria-labelledby="pref-updated-label"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-[#1e1f23] disabled:opacity-50 {{ $task_updated ? 'bg-purple-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                    <span class="sr-only">Toggle notification for General Details & Attachments</span>
                    <span aria-hidden="true" 
                          class="pointer-events-none flex items-center justify-center h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $task_updated ? 'translate-x-5' : 'translate-x-0' }}">
                        <span wire:loading wire:target="toggle('task_updated')" class="text-purple-600">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                    </span>
                </button>
            </div>

        </div>

        <div class="px-6 py-4 bg-slate-50/60 dark:bg-[#1a1b1f] border-t border-slate-200 dark:border-[#2b2c31] flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <span>Changes are saved immediately and take effect on all future activity notifications.</span>
            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Instant Sync
            </span>
        </div>
    </div>
</div>
