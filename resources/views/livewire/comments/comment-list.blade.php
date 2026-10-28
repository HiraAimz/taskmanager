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
                                                class="text-purple-600 dark:text-purple-400 hover:underline">
                                            Edit
                                        </button>
                                        <button wire:click="deleteComment({{ $comment->id }})"
                                                wire:confirm="Delete this comment?"
                                                class="text-rose-500 hover:underline">
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
                            <div class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                                {!! preg_replace(
                                    '/(https?:\/\/[^\s]+)/',
                                    '<a href="$1" target="_blank" class="text-purple-600 dark:text-purple-400 underline font-medium">$1</a>',
                                    e($comment->body ?? $comment->content)
                                ) !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-slate-400 text-xs py-3">
                No activity or comments yet.
            </p>
        @endforelse
    </div>
</div>
