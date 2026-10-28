<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-7xl mx-auto w-full space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span>🏢 Workspaces</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Multi-tenant workspace environments with project collections and team members.
                </p>
            </div>

            @can('create', App\Models\Workspace::class)
                <a href="{{ route('workspaces.create') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    New Workspace
                </a>
            @endcan
        </div>

        @if (session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if($workspaces->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($workspaces as $workspace)
                    <div class="group bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-5 shadow-xs hover:border-purple-400/80 dark:hover:border-purple-500/80 card-hover flex flex-col justify-between">
                        
                        <div>
                            <!-- Header Row with Avatar & Name -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-clickup-gradient flex items-center justify-center text-white text-base font-bold shadow-md">
                                        {{ strtoupper(substr($workspace->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                            <a href="{{ route('workspaces.edit', $workspace) }}">
                                                {{ $workspace->name }}
                                            </a>
                                        </h3>
                                        <span class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                            Owner: {{ $workspace->owner->name ?? 'Admin' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($workspace->description)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 line-clamp-2 leading-relaxed">
                                    {{ $workspace->description }}
                                </p>
                            @else
                                <p class="text-xs text-slate-400 dark:text-slate-500 italic mb-4">
                                    No description provided.
                                </p>
                            @endif
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-3 border-t border-slate-100 dark:border-[#25262c] flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-400">
                                {{ $workspace->projects_count ?? $workspace->projects->count() }} {{ Str::plural('project', $workspace->projects_count ?? $workspace->projects->count()) }}
                            </span>

                            <a href="{{ route('workspaces.edit', $workspace) }}" 
                               class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700">
                                Manage Workspace →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($workspaces->hasPages())
                <div class="pt-4">
                    {{ $workspaces->links() }}
                </div>
            @endif
        @else
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-16 text-center max-w-md mx-auto space-y-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center text-2xl">
                    🏢
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">No workspaces yet</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Create your first workspace to collaborate and organize projects.</p>
                @can('create', App\Models\Workspace::class)
                    <a href="{{ route('workspaces.create') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm">
                        + Create Workspace
                    </a>
                @endcan
            </div>
        @endif

    </div>
</div>
