<div class="space-y-3">
    <!-- File Upload Input -->
    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
        <input type="file" 
               wire:model="files"
               multiple
               id="file-upload"
               class="hidden">
        <label for="file-upload" class="cursor-pointer">
            <div class="space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="text-sm">
                    <span class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500">
                        Click to upload
                    </span>
                    <span> or drag and drop</span>
                </div>
                <p class="text-xs">
                    PDF, DOC, XLS, PNG, JPG, GIF, ZIP up to 10MB
                </p>
            </div>
        </label>

        @if($files)
            <div class="mt-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Uploading {{ count($files) }} file(s)...
                </span>
            </div>
        @endif
    </div>

    @error('files.*')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror

    <!-- Attachments List -->
    <div class="space-y-2">
        @forelse($attachments as $attachment)
            <div wire:key="attachment-{{ $attachment->id }}" 
                 class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <!-- File Icon -->
                    <div class="flex-shrink-0">
                        @if(str_starts_with($attachment->file_type, 'image/'))
                            <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>

                    <!-- File Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $attachment->file_name }}
                        </p>
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ number_format($attachment->file_size / 1024, 2) }} KB</span>
                            <span>•</span>
                            <span>{{ $attachment->user->name }}</span>
                            <span>•</span>
                            <span>{{ $attachment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 ml-3">
                    <a href="{{ route('attachments.download', $attachment) }}" 
                       class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>

                    @if($attachment->user_id === auth()->id() || auth()->user()->hasRole('Admin'))
                        <button wire:click="deleteAttachment({{ $attachment->id }})"
                                wire:confirm="Are you sure you want to delete this attachment?"
                                class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 text-sm py-4">
                No attachments yet.
            </p>
        @endforelse
    </div>
</div>
