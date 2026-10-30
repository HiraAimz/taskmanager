<div x-data
     @keydown.window.ctrl.k.prevent="$wire.open()"
     @keydown.window.meta.k.prevent="$wire.open()">
    <button type="button"
            wire:click="open"
            class="hidden items-center gap-2 rounded-lg border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs text-slate-500 transition-colors hover:border-purple-300 hover:bg-purple-50 hover:text-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500/30 dark:border-[#323338] dark:bg-[#242529] dark:text-slate-400 dark:hover:border-purple-700 dark:hover:bg-purple-950/30 dark:hover:text-purple-300 md:flex"
            aria-label="Search tasks"
            title="Search tasks (Ctrl+K)">
        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <span>Search tasks...</span>
        <kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 shadow-xs dark:border-slate-700 dark:bg-[#1a1b1e]">Ctrl K</kbd>
    </button>

    @if($isOpen)
        <div class="fixed inset-0 z-[100] flex items-start justify-center bg-slate-950/50 px-4 pt-[12vh] backdrop-blur-sm"
             wire:click.self="close"
             @keydown.escape.window="$wire.close()">
            <section class="w-full max-w-xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#35363b] dark:bg-[#1e1f23]"
                     role="dialog"
                     aria-modal="true"
                     aria-label="Search tasks">
                <div class="flex items-center gap-3 border-b border-slate-200 px-4 dark:border-[#35363b]">
                    <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search"
                           wire:model.live.debounce.250ms="query"
                           placeholder="Search task titles and descriptions..."
                           autofocus
                           class="min-w-0 flex-1 border-0 bg-transparent py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 dark:text-slate-100"
                           aria-label="Search task titles and descriptions">
                    <button type="button" wire:click="close"
                            class="rounded-lg px-2 py-1 text-xs text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500/40 dark:text-slate-400 dark:hover:bg-[#2a2b2f]">
                        Esc
                    </button>
                </div>

                <div class="max-h-[55vh] overflow-y-auto p-2">
                    @if(! $hasSearchTerm)
                        <p class="px-3 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            Type at least 2 characters to search tasks you can access.
                        </p>
                    @elseif($tasks->isEmpty())
                        <p class="px-3 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            No matching tasks found.
                        </p>
                    @else
                        <ul class="space-y-1">
                            @foreach($tasks as $task)
                                <li wire:key="global-task-search-{{ $task->id }}">
                                    <a href="{{ route('projects.list', $task->project_id) }}?task={{ $task->id }}"
                                       class="flex min-w-0 items-center gap-3 rounded-xl px-3 py-3 transition-colors hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500/40 dark:hover:bg-purple-950/30">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-300">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                            </svg>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $task->title }}</span>
                                            <span class="mt-0.5 flex min-w-0 items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                <span class="truncate">{{ $task->project->name }}</span>
                                                <span aria-hidden="true">·</span>
                                                <span class="shrink-0">{{ $task->status?->name ?? 'No status' }}</span>
                                            </span>
                                        </span>
                                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <footer class="border-t border-slate-200 px-4 py-2 text-[10px] text-slate-400 dark:border-[#35363b]">
                    Showing up to 8 matching tasks
                </footer>
            </section>
        </div>
    @endif
</div>
