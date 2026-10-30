<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-4xl mx-auto w-full space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('projects.board', $project) }}" class="text-xs text-slate-500 hover:text-purple-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to {{ $project->name }}
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Project Settings</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Configure project details, workflow stages, and project team members.</p>
            </div>
            
            <a href="{{ route('projects.statuses', $project) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-white dark:bg-[#1e1f23] border border-slate-200 dark:border-[#323338] rounded-xl hover:border-purple-400 text-purple-600 dark:text-purple-400 shadow-2xs transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Manage Statuses
            </a>
        </div>

        @if(session()->has('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><span>{{ session('success') }}</span></div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs font-semibold text-rose-700 dark:text-rose-300">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><span>{{ session('error') }}</span></div>
            </div>
        @endif

        <div class="grid gap-6">
            <!-- Project Details Card -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Project Information</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Basic details for space and board identification</p>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Project Name *
                        </label>
                        <input type="text" 
                               id="name"
                               wire:model="name" 
                               class="w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                               required>
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Description
                        </label>
                        <textarea id="description"
                                  wire:model="description" 
                                  rows="3"
                                  class="w-full text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl p-3 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"></textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="color" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Brand Accent Color
                        </label>
                        <div class="flex gap-3 items-center">
                            <input type="color" 
                                   id="color"
                                   wire:model.live="color" 
                                   class="h-9 w-16 bg-transparent border border-slate-200 dark:border-[#2f3036] rounded-xl cursor-pointer p-0.5">
                            <input type="text" 
                                   wire:model="color" 
                                   class="flex-1 text-xs font-mono bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200">
                        </div>
                        @error('color')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-[#25262c]">
                        <a href="{{ route('projects.index') }}" 
                           class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#28292e] rounded-xl transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-5 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Project Members Management Card -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Project Members</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Team members with explicit access to this project and its tasks</p>
                    </div>
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/50 px-2.5 py-1 rounded-full border border-purple-200/50 dark:border-purple-800/50">
                        {{ $projectMembers->count() }} {{ Str::plural('Member', $projectMembers->count()) }}
                    </span>
                </div>

                @can('manageMembers', $project)
                <!-- Add Member Form -->
                <div class="flex flex-col sm:flex-row gap-2.5 pb-5 border-b border-slate-100 dark:border-[#25262c]">
                    <div class="flex-1">
                        <select wire:model="selectedUserId"
                                class="form-select w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Select a user from {{ $project->workspace->name }}...</option>
                            @foreach($availableUsers as $wsUser)
                                <option value="{{ $wsUser->id }}">{{ $wsUser->name }} ({{ $wsUser->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <select wire:model="selectedRole" 
                                class="form-select w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <option value="member">Member</option>
                            <option value="admin">Project Admin</option>
                        </select>
                    </div>
                    <button wire:click="addMember"
                            type="button"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-semibold rounded-xl shadow-xs hover:bg-slate-800 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add to Project
                    </button>
                </div>
                @endcan

                <!-- Members List -->
                <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-[#28292e]">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-[#28292e]">
                        <thead class="bg-slate-50 dark:bg-[#16171a]">
                            <tr>
                                <th class="px-4 py-3 text-left text-2xs font-bold text-slate-500 uppercase tracking-wider">Member</th>
                                <th class="px-4 py-3 text-left text-2xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-2xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                                <th class="px-4 py-3 text-right text-2xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#24252a] bg-white dark:bg-[#1a1b1e]">
                            @forelse($projectMembers as $member)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#1f2024] transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-2xs font-bold shadow-2xs">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </div>
                                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $member->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $member->email }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($member->id === $project->owner_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            Owner
                                        </span>
                                    @elseif($member->pivot->role === 'admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            Member
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($member->id !== $project->owner_id)
                                        @can('manageMembers', $project)
                                        <button wire:click="removeMember({{ $member->id }})"
                                                wire:confirm="Remove {{ $member->name }} from this project?"
                                                class="text-xs font-semibold text-rose-500 hover:text-rose-700 transition-colors">
                                            Remove
                                        </button>
                                        @endcan
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-xs text-slate-400">
                                    No explicit members assigned.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
