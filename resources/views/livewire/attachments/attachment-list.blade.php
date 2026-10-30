<div class="space-y-3">
    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-5 text-center transition-colors hover:border-purple-400 dark:border-[#2f3036] dark:bg-[#16171a] dark:hover:border-purple-500">
        <input type="file"
               wire:model="files"
               multiple
               accept=".avif,.bmp,.csv,.doc,.docx,.gif,.jpg,.jpeg,.md,.pdf,.png,.ppt,.pptx,.svg,.txt,.webp,.xls,.xlsx,.zip"
               id="file-upload"
               class="hidden">
        <label for="file-upload" class="block cursor-pointer">
            <div class="space-y-1.5 text-slate-500 dark:text-slate-400">
                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950/40">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <div class="text-xs">
                    <span class="font-bold text-purple-600 hover:underline dark:text-purple-400">Choose files</span>
                    <span> or drag and drop</span>
                </div>
                <p class="text-[10px] text-slate-400">Images, PDF, Office documents, text files, ZIP up to 10MB</p>
            </div>
        </label>

        <div wire:loading wire:target="files" class="mt-2 text-xs font-semibold text-purple-600 dark:text-purple-400" role="status">
            Uploading selected files...
        </div>
    </div>

    @error('files.*')
        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror

    <div class="grid min-w-0 grid-cols-1 gap-2 sm:grid-cols-2">
        @forelse($attachments as $attachment)
            <x-attachment-card
                :attachment="$attachment"
                :preview-url="route('attachments.preview', $attachment)"
                :download-url="route('attachments.download', $attachment)"
                :delete-action="$attachment->user_id === auth()->id() || auth()->user()->hasRole('Admin') || auth()->user()->can('attachment.delete') ? 'deleteAttachment('.$attachment->id.')' : null" />
        @empty
            <p class="col-span-full py-3 text-center text-xs text-slate-400">No attachments uploaded.</p>
        @endforelse
    </div>
</div>
