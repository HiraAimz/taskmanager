<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-4xl mx-auto w-full space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('workspaces.index') }}" class="text-xs text-slate-500 hover:text-purple-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Workspaces / {{ $workspace->name }}
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Workspace Settings</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Manage workspace details and team member permissions.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><span>{{ session('success') }}</span></div>
            </div>
        @endif

        <div class="grid gap-6">
            <!-- Workspace Details Card -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">General Information</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Basic details about your workspace</p>
                </div>

                <form wire:submit="update" class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Workspace Name *
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

                    <div class="flex justify-end pt-3 border-t border-slate-100 dark:border-[#25262c]">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 px-5 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all disabled:opacity-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span wire:loading.remove wire:target="update">Save Changes</span>
                            <span wire:loading wire:target="update">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Team Members Card -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Team Members</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Collaborators who have access to this workspace and its projects</p>
                    </div>
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/50 px-2.5 py-1 rounded-full border border-purple-200/50 dark:border-purple-800/50">
                        {{ $workspace->users->count() }} {{ Str::plural('Member', $workspace->users->count()) }}
                    </span>
                </div>

                <!-- Add Member Form -->
                <div class="flex gap-2.5 mb-6 pb-6 border-b border-slate-100 dark:border-[#25262c]">
                    <div class="flex-1">
                        <select wire:model="selectedUserId"
                                id="selectedUserId"
                                class="w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
                            <option value="">Select a user to invite...</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button wire:click="addMember"
                            type="button"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-slate-800 text-xs font-semibold rounded-xl shadow-xs transition-all disabled:opacity-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Member
                    </button>
                </div>

                <!-- Members List -->
                @if($workspace->users->count() > 0)
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-[#28292e]">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-[#28292e]">
                            <thead class="bg-slate-50 dark:bg-[#16171a]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-2xs font-bold text-slate-500 uppercase tracking-wider">
                                        Member
                                    </th>
                                    <th class="px-4 py-3 text-left text-2xs font-bold text-slate-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-4 py-3 text-left text-2xs font-bold text-slate-500 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th class="px-4 py-3 text-right text-2xs font-bold text-slate-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-[#24252a] bg-white dark:bg-[#1a1b1e]">
                                @foreach($workspace->users as $member)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-[#1f2024] transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 flex items-center justify-center text-white text-2xs font-bold shadow-2xs">
                                                {{ strtoupper(substr($member->name, 0, 2)) }}
                                            </div>
                                            <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $member->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                                        {{ $member->email }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($member->id === $workspace->owner_id)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                Owner
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                Member
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($member->id !== $workspace->owner_id)
                                            <button wire:click="removeMember({{ $member->id }})"
                                                    wire:confirm="Are you sure you want to remove {{ $member->name }} from this workspace?"
                                                    class="text-xs font-semibold text-rose-500 hover:text-rose-700 transition-colors">
                                                Remove
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-xs text-slate-500 dark:text-slate-400">
                        No team members added yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
