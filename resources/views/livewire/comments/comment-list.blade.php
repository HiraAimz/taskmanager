<div class="space-y-4">
    <!-- Add Comment Form -->
    <form wire:submit="addComment" class="mb-4">
        <div class="flex gap-2.5">
            <div class="flex-shrink-0">
                <div class="w-7 h-7 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-[11px] font-bold shadow-2xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex-1 space-y-2">
                <textarea wire:model="newComment"
                          rows="2"
                          class="w-full text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl p-2.5 text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder-slate-400 font-medium"
                          placeholder="Write a comment or update..."></textarea>
                @error('newComment')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror

                <!-- File attachments for comment -->
                <div x-data="{ dragover: false }"
                     @dragover.prevent="dragover = true"
                     @dragleave.prevent="dragover = false"
                     @drop.prevent="dragover = false"
                     class="relative">
                    <label for="comment-file-upload"
                           :class="dragover ? 'border-purple-500 bg-purple-50 dark:bg-purple-950/20' : 'border-slate-200 dark:border-[#2f3036] hover:border-purple-400'"
                           class="flex items-center gap-2 px-3 py-1.5 border border-dashed rounded-xl cursor-pointer transition-colors text-xs text-slate-500 dark:text-slate-400">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <span>Attach files <span class="text-slate-400 text-[10px]">(up to 5, 10MB each)</span></span>
                    </label>
                    <input type="file"
                           id="comment-file-upload"
                           wire:model="commentFiles"
                           multiple
                           class="hidden">
                </div>

                @error('commentFiles.*')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror

                <!-- Preview selected files -->
                @if(!empty($commentFiles))
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($commentFiles as $i => $file)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-[10px] font-semibold text-purple-700 dark:text-purple-300">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $file->getClientOriginalName() }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-3.5 py-1.5 bg-clickup-gradient text-white rounded-xl text-xs font-semibold hover:opacity-95 shadow-2xs transition-all">
                        Comment
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Comments Stream -->
    <div class="space-y-3">
        @forelse($comments as $comment)
            <div wire:key="comment-{{ $comment->id }}" class="flex gap-2.5">
                <!-- User Avatar -->
                <div class="flex-shrink-0">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[11px] font-bold shadow-2xs">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>
                </div>

                <!-- Comment Content -->
                <div class="flex-1 min-w-0">
                    <div class="bg-slate-50 dark:bg-[#16171a] border border-slate-200/80 dark:border-[#2f3036] rounded-xl p-3">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-bold text-xs text-slate-900 dark:text-white">
                                {{ $comment->user->name }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-slate-400">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                                
                                @if($comment->user_id === auth()->id() || auth()->user()->hasRole('Admin'))
                                    <div class="flex gap-1.5 text-[10px]">
                                        <button wire:click="startEdit({{ $comment->id }})"
                                                class="inline-flex items-center gap-0.5 text-purple-600 dark:text-purple-400 hover:underline">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button wire:click="deleteComment({{ $comment->id }})"
                                                wire:confirm="Delete this comment?"
                                                class="inline-flex items-center gap-0.5 text-rose-500 hover:underline">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($editingCommentId === $comment->id)
                            <!-- Edit Form -->
                            <div class="mt-2 space-y-2">
                                <textarea wire:model="editingContent"
                                          rows="2"
                                          class="w-full text-xs bg-white dark:bg-[#202126] border border-purple-500 rounded-lg p-2 text-slate-800 dark:text-slate-100"></textarea>
                                <div class="flex gap-2">
                                    <button wire:click="saveEdit"
                                            class="px-2.5 py-1 bg-clickup-gradient text-white rounded-lg text-xs font-semibold">
                                        Save
                                    </button>
                                    <button wire:click="cancelEdit"
                                            class="px-2.5 py-1 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-xs">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Comment Text -->
                            @if(trim($comment->body ?? $comment->content))
                                <div class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                                    {!! preg_replace(
                                        '/(https?:\/\/[^\s]+)/',
                                        '<a href="$1" target="_blank" class="text-purple-600 dark:text-purple-400 underline font-medium">$1</a>',
                                        e($comment->body ?? $comment->content)
                                    ) !!}
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Comment Attachments -->
                    @if($comment->attachments->count() > 0)
                        @php
                            $imageAttachments = $comment->attachments->filter(fn($a) => str_starts_with($a->file_type ?? '', 'image/'));
                            $fileAttachments = $comment->attachments->reject(fn($a) => str_starts_with($a->file_type ?? '', 'image/'));
                        @endphp

                        <!-- Image Previews Grid -->
                        @if($imageAttachments->isNotEmpty())
                            <div class="mt-2.5 flex flex-wrap gap-2.5 pl-1">
                                @foreach($imageAttachments as $img)
                                    <div wire:key="ca-img-{{ $img->id }}" 
                                         x-data="{ failed: false }"
                                         class="relative group rounded-xl overflow-hidden border border-slate-200 dark:border-[#2f3036] bg-slate-100 dark:bg-[#16171a] shadow-xs max-w-[240px]">
                                        <!-- Image Display -->
                                        <template x-if="!failed">
                                            <a href="{{ route('comment-attachments.preview', $img) }}" target="_blank" class="block overflow-hidden cursor-zoom-in">
                                                <img src="{{ route('comment-attachments.preview', $img) }}" 
                                                     alt="{{ $img->original_name }}"
                                                     @error="failed = true"
                                                     loading="lazy"
                                                     class="max-h-48 w-full object-cover rounded-xl group-hover:scale-102 transition-transform duration-200">
                                            </a>
                                        </template>

                                        <!-- Fallback if image fails to load -->
                                        <template x-if="failed">
                                            <div class="p-3 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="truncate max-w-[120px]">{{ $img->original_name }}</span>
                                            </div>
                                        </template>

                                        <!-- Hover Action Overlay -->
                                        <div class="absolute inset-x-0 bottom-0 p-1.5 bg-gradient-to-t from-black/70 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-between text-white text-[10px]">
                                            <span class="truncate px-1 font-medium">{{ $img->original_name }}</span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <a href="{{ route('comment-attachments.download', $img) }}" title="Download" class="p-1 hover:text-purple-300 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                                @if($comment->user_id === auth()->id() || auth()->user()->hasRole('Admin'))
                                                    <button type="button" 
                                                            wire:click="deleteCommentAttachment({{ $img->id }})" 
                                                            wire:confirm="Remove this attachment?"
                                                            title="Delete"
                                                            class="p-1 hover:text-rose-400 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Non-Image Files List -->
                        @if($fileAttachments->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-1.5 pl-1">
                                @foreach($fileAttachments as $att)
                                    <div wire:key="ca-{{ $att->id }}" class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-[#1e1f23] border border-slate-200 dark:border-[#2f3036] shadow-2xs group">
                                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-[10px] font-medium text-slate-700 dark:text-slate-300 max-w-[120px] truncate">
                                            {{ $att->original_name }}
                                        </span>
                                        <span class="text-[9px] text-slate-400">{{ $att->file_size_kb }} KB</span>
                                        <a href="{{ route('comment-attachments.download', $att) }}"
                                           class="text-purple-500 hover:text-purple-700 transition-colors ml-0.5"
                                           title="Download">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        @if($comment->user_id === auth()->id() || auth()->user()->hasRole('Admin'))
                                            <button wire:click="deleteCommentAttachment({{ $att->id }})"
                                                    wire:confirm="Remove this attachment?"
                                                    class="text-slate-400 hover:text-rose-500 transition-colors ml-0.5"
                                                    title="Remove attachment">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-slate-400 text-xs py-3">
                No activity or comments yet.
            </p>
        @endforelse
    </div>
</div>
