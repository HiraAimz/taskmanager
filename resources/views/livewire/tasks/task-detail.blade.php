<div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
     wire:click="close"
     x-data
     x-init="document.body.style.overflow = 'hidden'"
     x-destroy="document.body.style.overflow = 'auto'">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col"
         wire:click.stop
         x-data
         @keydown.escape.window="$wire.close()">
        
        <!-- Header -->
        <div class="relative px-8 pt-8 pb-6 border-b border-gray-200">
            <!-- Close Button -->
            <button wire:click="close" 
                    class="absolute top-6 right-6 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Title Input -->
            <input type="text" 
                   wire:model.blur="title"
                   class="text-3xl font-bold bg-transparent border-none focus:ring-0 p-0 w-full text-gray-900 placeholder-gray-400"
                   placeholder="Task title">
            @error('title')
                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror

            <!-- Meta Info -->
            <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Created {{ $task->created_at->diffForHumans() }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Updated {{ $task->updated_at->diffForHumans() }}
                </span>
            </div>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-3 gap-8 p-8">
                
                <!-- Main Content (2 columns) -->
                <div class="col-span-2 space-y-8">
                    
                    <!-- Description -->
                    <div class="group">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            Description
                        </label>
                        <textarea wire:model.blur="description"
                                  rows="5"
                                  class="w-full border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-xl shadow-sm transition-all duration-200"
                                  placeholder="Add a detailed description..."></textarea>
                    </div>

                    <!-- Checklists -->
                    <div>
                        <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Checklist
                        </h3>
                        <livewire:checklists.checklist-manager :task="$task" :key="'checklist-'.$task->id" />
                    </div>

                    <!-- Comments -->
                    <div>
                        <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Comments
                        </h3>
                        <livewire:comments.comment-list :task="$task" :key="'comments-'.$task->id" />
                    </div>

                    <!-- Attachments -->
                    <div>
                        <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            Attachments
                        </h3>
                        <livewire:attachments.attachment-upload :task="$task" :key="'attachments-'.$task->id" />
                    </div>
                </div>

                <!-- Sidebar (1 column) -->
                <div class="space-y-6">
                    
                    <!-- Status -->
                    <div>
                        <label class="flex items-center gap-2 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status
                        </label>
                        <select wire:model.live="statusId"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 text-sm font-medium">
                            @foreach($projectStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="flex items-center gap-2 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Priority
                        </label>
                        <select wire:model.live="priority"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 text-sm font-medium">
                            <option value="urgent">🔴 Urgent</option>
                            <option value="high">🟠 High</option>
                            <option value="normal">🔵 Normal</option>
                            <option value="low">⚪ Low</option>
                        </select>
                    </div>

                    <!-- Assignee -->
                    <div>
                        <label class="flex items-center gap-2 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Assignee
                        </label>
                        <select wire:model.live="assigneeId"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 text-sm">
                            <option value="">Unassigned</option>
                            @foreach($workspaceMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-gray-500">Primary person responsible</p>
                    </div>

                    <!-- Collaborators -->
                    <div>
                        <label class="flex items-center gap-2 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Collaborators
                        </label>
                        
                        <!-- Selected Collaborators -->
                        @if(count($selectedAssignees) > 0)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($workspaceMembers->whereIn('id', $selectedAssignees) as $collaborator)
                                    <div class="group inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-blue-800 text-sm rounded-full hover:shadow-md transition-all duration-200">
                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ strtoupper(substr($collaborator->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium">{{ $collaborator->name }}</span>
                                        <button type="button" 
                                                wire:click="$set('selectedAssignees', {{ json_encode(array_values(array_diff($selectedAssignees, [$collaborator->id]))) }})"
                                                class="opacity-0 group-hover:opacity-100 text-blue-600 hover:text-red-600 transition-all duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Collaborator Selection -->
                        <div class="space-y-1 max-h-52 overflow-y-auto border border-gray-200 rounded-xl bg-gray-50 p-2">
                            @foreach($workspaceMembers as $member)
                                <label class="flex items-center gap-3 p-2.5 hover:bg-white rounded-lg cursor-pointer transition-all duration-200 group">
                                    <input type="checkbox" 
                                           wire:model.live="selectedAssignees"
                                           value="{{ $member->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm group-hover:shadow-md transition-all duration-200">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ $member->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Select team members to collaborate</p>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="flex items-center gap-2 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Due Date
                        </label>
                        <input type="date" 
                               wire:model.live="dueDate"
                               class="w-full border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 text-sm">
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 p-6 bg-gray-50">
            <div class="flex items-center justify-between gap-4">
                <div>
                    @if (session()->has('message'))
                        <div class="flex items-center gap-2 text-sm text-green-700 bg-green-50 px-4 py-2 rounded-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('message') }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    @can('delete', $task)
                        <button wire:click="delete"
                                wire:confirm="Are you sure you want to delete this task?"
                                class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    @endcan

                    <button wire:click="save"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
