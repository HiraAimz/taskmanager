<div>
    <!-- Quick Create Modal -->
    @if($isOpen)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
             wire:click="close"
             x-data
             @keydown.escape.window="$wire.close()">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all"
                 wire:click.stop>
                <!-- Header with gradient -->
                <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 rounded-t-xl">
                    <h3 class="text-lg font-bold text-white">Quick Add Task</h3>
                    <button wire:click="close" 
                            class="p-1 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
                            Task Name *
                        </label>
                        <input type="text" 
                               id="title"
                               wire:model="title" 
                               class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 placeholder-gray-400 transition-all"
                               placeholder="What needs to be done?"
                               autofocus>
                        @error('title')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                            Description
                        </label>
                        <textarea id="description"
                                  wire:model="description" 
                                  rows="3"
                                  class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 placeholder-gray-400 resize-none transition-all"
                                  placeholder="Add more details..."></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 mt-6">
                        <button type="button"
                                wire:click="close"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-2 text-sm font-medium bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg shadow-sm transition-all">
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
