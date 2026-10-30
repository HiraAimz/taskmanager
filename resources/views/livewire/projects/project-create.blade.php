<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-2xl mx-auto w-full space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('projects.index') }}" class="text-xs text-slate-500 hover:text-purple-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Projects
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Create New Project</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Set up a space or project to organize tasks, lists, and workflows.</p>
            </div>
        </div>

        @if($workspaces->isEmpty())
            <!-- No Workspace Warning -->
            <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-900/50 rounded-xl text-amber-700 dark:text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">Workspace Required</h3>
                        <p class="mt-1 text-xs text-amber-700 dark:text-amber-300">You need to create or join a workspace before creating projects.</p>
                        <div class="mt-4">
                            <a href="{{ route('workspaces.create') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-xs hover:opacity-95 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Create Workspace First
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Form Card -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs">
                <form wire:submit="save" class="space-y-4">
                    <!-- Workspace Selection -->
                    <div>
                        <label for="workspaceId" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Workspace *
                        </label>
                        <select wire:model="workspaceId"
                                id="workspaceId"
                                class="form-select w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer"
                                required>
                            <option value="">Choose a workspace...</option>
                            @foreach($workspaces as $workspace)
                                <option value="{{ $workspace->id }}">{{ $workspace->name }}</option>
                            @endforeach
                        </select>
                        @error('workspaceId')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Project Name *
                        </label>
                        <input type="text" 
                               id="name"
                               wire:model="name" 
                               placeholder="e.g. Sprint Engineering, Product Roadmap"
                               class="w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                               required>
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Description <span class="text-slate-400 font-normal lowercase">(optional)</span>
                        </label>
                        <textarea id="description"
                                  wire:model="description" 
                                  rows="3"
                                  placeholder="Describe the project purpose and deliverables..."
                                  class="w-full text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl p-3 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"></textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color Picker -->
                    <div>
                        <label for="color" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Project Color
                        </label>
                        <div class="flex gap-3 items-center">
                            <input type="color" 
                                   id="color"
                                   wire:model.live="color" 
                                   class="h-9 w-16 bg-transparent border border-slate-200 dark:border-[#2f3036] rounded-xl cursor-pointer p-0.5">
                            <input type="text" 
                                   wire:model="color" 
                                   placeholder="#7b68ee"
                                   class="flex-1 text-xs font-mono bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200">
                        </div>
                        @error('color')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="p-3 bg-purple-50 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/40 rounded-xl text-xs text-purple-700 dark:text-purple-300 flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Default workflow stages (To Do, In Progress, In Review, Done) will be automatically generated.
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-[#25262c]">
                        <a href="{{ route('projects.index') }}" 
                           class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#28292e] rounded-xl transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 px-5 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all disabled:opacity-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span wire:loading.remove wire:target="save">Create Project</span>
                            <span wire:loading wire:target="save">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
