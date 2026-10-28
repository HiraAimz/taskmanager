<div class="space-y-3">
    <!-- File Upload Dropzone (ClickUp Style) -->
    <div class="border-2 border-dashed border-slate-200 dark:border-[#2f3036] hover:border-purple-400 dark:hover:border-purple-500 rounded-2xl p-5 text-center bg-slate-50/50 dark:bg-[#16171a] transition-colors">
        <input type="file" 
               wire:model="files"
               multiple
               id="file-upload"
               class="hidden">
        <label for="file-upload" class="cursor-pointer block">
            <div class="space-y-1.5 text-slate-500 dark:text-slate-400">
                <div class="w-10 h-10 mx-auto rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <div class="text-xs">
                    <span class="font-bold text-purple-600 dark:text-purple-400 hover:underline">
                        Choose files
                    </span>
                    <span> or drag and drop</span>
                </div>
                <p class="text-[10px] text-slate-400">
                    PDF, DOC, XLS, PNG, JPG, ZIP up to 10MB
                </p>
            </div>
        </label>

        @if($files)
            <div class="mt-2 text-xs font-semibold text-purple-600 dark:text-purple-400 animate-pulse">
                Uploading {{ count($files) }} file(s)...
            </div>
        @endif
    </div>

    @error('files.*')
        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
    @enderror

    <!-- Attachments List -->
    <div class="space-y-2">
        @forelse($attachments as $attachment)
            <div wire:key="attachment-{{ $attachment->id }}" 
                 class="flex items-center justify-between p-3 bg-slate-50 dark:bg-[#16171a] border border-slate-200/80 dark:border-[#2f3036] rounded-xl">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <!-- File Icon -->
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                        @if(str_starts_with($attachment->file_type, 'image/'))
                            🖼️
                        @else
                            📄
                        @endif
                    </div>

                    <!-- File Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $attachment->file_name }}
                        </p>
                        <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                            <span>{{ number_format($attachment->file_size / 1024, 1) }} KB</span>
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
                       class="p-1.5 text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 hover:bg-white dark:hover:bg-[#202126] rounded-lg transition-colors"
                       title="Download">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>

                    @if($attachment->user_id === auth()->id() || auth()->user()->hasRole('Admin'))
                        <button wire:click="deleteAttachment({{ $attachment->id }})"
                                wire:confirm="Delete this attachment?"
                                class="p-1.5 text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-white dark:hover:bg-[#202126] rounded-lg transition-colors"
                                title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-slate-400 text-xs py-2">
                No attachments uploaded.
            </p>
        @endforelse
    </div>
</div>
