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
                    PDF, DOC, XLS, PNG, JPG, GIF, ZIP up to 10MB
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
            @php
                $isImg = str_starts_with($attachment->file_type ?? '', 'image/');
            @endphp
            <div wire:key="attachment-{{ $attachment->id }}" 
                 x-data="{ imgFailed: false }"
                 class="flex items-center justify-between p-3 bg-slate-50 dark:bg-[#16171a] border border-slate-200/80 dark:border-[#2f3036] rounded-xl hover:border-slate-300 dark:hover:border-[#383940] transition-colors">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <!-- File / Image Preview Thumbnail -->
                    @if($isImg)
                        <div class="relative w-12 h-12 rounded-lg overflow-hidden border border-slate-200 dark:border-[#2f3036] bg-slate-100 dark:bg-[#202126] flex items-center justify-center shrink-0">
                            <template x-if="!imgFailed">
                                <a href="{{ route('attachments.preview', $attachment) }}" target="_blank" class="block w-full h-full cursor-zoom-in">
                                    <img src="{{ route('attachments.preview', $attachment) }}" 
                                         alt="{{ $attachment->file_name }}"
                                         @error="imgFailed = true"
                                         loading="lazy"
                                         class="w-full h-full object-cover">
                                </a>
                            </template>
                            <template x-if="imgFailed">
                                <div class="w-full h-full flex items-center justify-center text-blue-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center shrink-0">
                            @if(str_contains($attachment->file_type ?? '', 'pdf'))
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            @endif
                        </div>
                    @endif

                    <!-- File Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $attachment->file_name }}
                        </p>
                        <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                            <span>{{ number_format($attachment->file_size / 1024, 1) }} KB</span>
                            <span>•</span>
                            <span>{{ $attachment->user?->name ?? 'User' }}</span>
                            <span>•</span>
                            <span>{{ $attachment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1.5 ml-3 shrink-0">
                    @if($isImg)
                        <a href="{{ route('attachments.preview', $attachment) }}" 
                           target="_blank"
                           class="p-1.5 text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 hover:bg-white dark:hover:bg-[#202126] rounded-lg transition-colors"
                           title="Open image preview">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                    @endif

                    <a href="{{ route('attachments.download', $attachment) }}" 
                       class="p-1.5 text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 hover:bg-white dark:hover:bg-[#202126] rounded-lg transition-colors"
                       title="Download">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>

                    @if($attachment->user_id === auth()->id() || auth()->user()->hasRole('Admin') || auth()->user()->can('attachment.delete'))
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
