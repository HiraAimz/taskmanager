<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-7xl mx-auto w-full space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <svg class="w-6 h-6 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg><span>Projects &amp; Spaces</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Organize tasks, collaborate with team members, and track workflows across spaces.
                </p>
            </div>

            @can('create', \App\Models\Project::class)
                <a href="{{ route('projects.create') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    New Project
                </a>
            @endcan
        </div>

        @if($projects->isEmpty())
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-16 text-center max-w-md mx-auto space-y-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg></div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">No projects yet</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Create your first space to start organizing your tasks and boards.</p>
                @can('create', \App\Models\Project::class)
                    <a href="{{ route('projects.create') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm">
                        + Create First Project
                    </a>
                @endcan
            </div>
        @else
            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($projects as $project)
                    <div class="group bg-white dark:bg-[#1a1b1e] border border-slate-200/80 dark:border-[#2a2b2f] rounded-2xl overflow-hidden shadow-xs hover:border-purple-400/80 dark:hover:border-purple-500/80 card-hover flex flex-col justify-between">
                        
                        <!-- Top Color Stripe -->
                        <div class="h-2 w-full" style="background-color: {{ $project->color ?? '#7b68ee' }}"></div>

                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Header Row -->
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shadow-2xs" 
                                             style="background-color: {{ $project->color ?? '#7b68ee' }}18">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                                <a href="{{ route('projects.board', $project) }}">
                                                    {{ $project->name }}
                                                </a>
                                            </h3>
                                            <p class="text-[11px] text-slate-400 flex items-center gap-1">
                                                <span>{{ $project->workspace->name }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    @can('update', $project)
                                        <a href="{{ route('projects.edit', $project) }}" 
                                           class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#25262a] rounded-lg transition-colors"
                                           title="Project Settings">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </a>
                                    @endcan
                                </div>

                                @if($project->description)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 line-clamp-2 leading-relaxed">
                                        {{ $project->description }}
                                    </p>
                                @endif
                            </div>

                            <!-- Meta Info & Views Shortcut Bar -->
                            <div class="pt-3 border-t border-slate-100 dark:border-[#25262c]">
                                <div class="flex items-center justify-between text-[11px] text-slate-400 mb-3">
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $project->tasks_count }} {{ Str::plural('task', $project->tasks_count) }}
                                    </span>
                                    <span>Created {{ $project->created_at->diffForHumans() }}</span>
                                </div>

                                <!-- Quick Jump Buttons -->
                                <div class="grid grid-cols-3 gap-1.5 bg-slate-50 dark:bg-[#202126] p-1 rounded-xl">
                                    <a href="{{ route('projects.board', $project) }}" 
                                       class="text-center py-1.5 text-xs font-semibold rounded-lg bg-white dark:bg-[#28292e] text-purple-600 dark:text-purple-400 shadow-2xs hover:bg-purple-50 dark:hover:bg-purple-950/40 transition-colors">
                                        Board
                                    </a>
                                    <a href="{{ route('projects.list', $project) }}" 
                                       class="text-center py-1.5 text-xs font-semibold rounded-lg text-slate-600 dark:text-slate-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-white dark:hover:bg-[#28292e] transition-colors">
                                        List
                                    </a>
                                    <a href="{{ route('projects.calendar', $project) }}" 
                                       class="text-center py-1.5 text-xs font-semibold rounded-lg text-slate-600 dark:text-slate-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-white dark:hover:bg-[#28292e] transition-colors">
                                        Calendar
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            @if($projects->hasPages())
                <div class="pt-4">
                    {{ $projects->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
