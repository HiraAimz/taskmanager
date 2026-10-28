<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 p-4 sm:p-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('projects.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-purple-600 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Projects
            </a>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                Create New Project
            </h1>
            <p class="text-gray-600 text-lg">Set up a new project to organize your tasks and workflows</p>
        </div>

        @if($workspaces->isEmpty())
            <!-- No Workspace Warning -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-6 shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-amber-900">Workspace Required</h3>
                        <p class="mt-2 text-amber-800">You need to be a member of a workspace before creating projects. Workspaces help organize your team and projects.</p>
                        <div class="mt-4">
                            <a href="{{ route('workspaces.create') }}" 
                               class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-pink-700 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create Your First Workspace
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <form wire:submit="save" class="p-8 space-y-8">
                    <!-- Workspace Selection -->
                    <div class="relative group">
                        <label for="workspaceId" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Select Workspace</span>
                            <span class="ml-1 text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select wire:model="workspaceId"
                                    id="workspaceId"
                                    class="block w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-xl 
                                           focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10
                                           text-gray-900 appearance-none cursor-pointer
                                           transition-all duration-200
                                           hover:border-gray-300
                                           @error('workspaceId') border-red-300 focus:border-red-500 focus:ring-red-500/10 @enderror"
                                    required>
                                <option value="">Choose a workspace...</option>
                                @foreach($workspaces as $workspace)
                                    <option value="{{ $workspace->id }}">{{ $workspace->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('workspaceId')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Projects belong to workspaces and inherit their members</p>
                    </div>

                    <!-- Project Name -->
                    <div class="relative group">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Project Name</span>
                            <span class="ml-1 text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="name"
                                   wire:model="name" 
                                   placeholder="e.g. Website Redesign, Q4 Marketing Campaign"
                                   class="block w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-xl 
                                          focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10
                                          text-gray-900 placeholder-gray-400
                                          transition-all duration-200
                                          hover:border-gray-300
                                          @error('name') border-red-300 focus:border-red-500 focus:ring-red-500/10 @enderror"
                                   required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                @error('name')
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                @enderror
                            </div>
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Give your project a clear, descriptive name</p>
                    </div>

                    <!-- Description -->
                    <div class="relative group">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-3">
                            Description
                            <span class="text-gray-400 font-normal ml-1">(Optional)</span>
                        </label>
                        <div class="relative">
                            <textarea id="description"
                                      wire:model="description" 
                                      rows="5"
                                      placeholder="Describe the project goals, scope, and key deliverables..."
                                      class="block w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-xl 
                                             focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10
                                             text-gray-900 placeholder-gray-400 resize-none
                                             transition-all duration-200
                                             hover:border-gray-300
                                             @error('description') border-red-300 focus:border-red-500 focus:ring-red-500/10 @enderror"></textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Help your team understand the project's purpose and objectives</p>
                    </div>

                    <!-- Color Picker -->
                    <div class="relative group">
                        <label for="color" class="block text-sm font-semibold text-gray-700 mb-3">
                            Project Color
                        </label>
                        <div class="flex gap-4 items-start">
                            <div class="relative">
                                <input type="color" 
                                       id="color"
                                       wire:model.live="color" 
                                       class="h-14 w-14 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-full border-2 border-gray-200 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="text" 
                                       wire:model="color" 
                                       placeholder="#3b82f6"
                                       class="block w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-xl 
                                              focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10
                                              text-gray-900 placeholder-gray-400 font-mono
                                              transition-all duration-200
                                              hover:border-gray-300">
                                <p class="mt-2 text-xs text-gray-500">Choose a color to visually identify your project</p>
                            </div>
                        </div>
                        @error('color')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-100 rounded-xl p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-purple-900">What's next?</h3>
                                <p class="mt-1 text-sm text-purple-700">After creating your project, you can customize statuses, create task lists, and start adding tasks.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                        <a href="{{ route('projects.index') }}" 
                           class="px-6 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all">
                            Cancel
                        </a>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="group relative px-8 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 
                                       text-white text-sm font-semibold rounded-xl shadow-lg shadow-purple-500/30
                                       transition-all duration-200 transform hover:scale-105 hover:shadow-xl hover:shadow-purple-500/40
                                       disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                            <span wire:loading.remove wire:target="save" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Project
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Text -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Need help? 
                    <a href="#" class="text-purple-600 hover:text-purple-700 font-medium">Learn about project management</a>
                </p>
            </div>
        @endif
    </div>
</div>
