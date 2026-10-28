<div class="space-y-4">
    <!-- Add Comment Form -->
    <form wire:submit="addComment" class="mb-4">
        <div class="flex gap-2">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex-1">
                <textarea wire:model="newComment"
                          rows="2"
                          class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm text-sm"
                          placeholder="Add a comment..."></textarea>
                @error('newComment')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div class="mt-2">
                    <button type="submit"
                            class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        Comment
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Comments List -->
    <div class="space-y-3">
        @forelse($comments as $comment)
            <div wire:key="comment-{{ $comment->id }}" class="flex gap-2">
                <!-- User Avatar -->
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center text-white text-sm font-semibold">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>
                </div>

                <!-- Comment Content -->
                <div class="flex-1">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-sm text-gray-900 dark:text-gray-100">
                                {{ $comment->user->name }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                                
                                @if($comment->user_id === auth()->id() || auth()->user()->hasRole('Admin'))
                                    <div class="flex gap-1">
                                        <button wire:click="startEdit({{ $comment->id }})"
                                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                            Edit
                                        </button>
                                        <button wire:click="deleteComment({{ $comment->id }})"
                                                wire:confirm="Are you sure you want to delete this comment?"
                                                class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                            Delete
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($editingCommentId === $comment->id)
                            <!-- Edit Form -->
                            <div class="mt-2">
                                <textarea wire:model="editingContent"
                                          rows="2"
                                          class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm text-sm"></textarea>
                                <div class="mt-2 flex gap-2">
                                    <button wire:click="saveEdit"
                                            class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                        Save
                                    </button>
                                    <button wire:click="cancelEdit"
                                            class="px-3 py-1 bg-gray-600 text-white rounded text-xs hover:bg-gray-700">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Comment Text with Auto-linked URLs -->
                            <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                {!! preg_replace(
                                    '/(https?:\/\/[^\s]+)/',
                                    '<a href="$1" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">$1</a>',
                                    e($comment->content)
                                ) !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 text-sm py-4">
                No comments yet. Be the first to comment!
            </p>
        @endforelse
    </div>
</div>
