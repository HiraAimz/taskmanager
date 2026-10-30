@props([
    'attachment',
    'previewUrl',
    'downloadUrl',
    'deleteAction' => null,
    'compact' => false,
])

@php
    $name = $attachment->original_name ?? $attachment->file_name ?? 'Attachment';
    $mime = $attachment->file_type ?? $attachment->mime_type ?? '';
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $isImage = in_array($extension, ['avif', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'webp'], true);
    $size = (int) ($attachment->file_size ?? $attachment->size ?? 0);
    $sizeLabel = $size >= 1048576 ? number_format($size / 1048576, 1).' MB' : number_format($size / 1024, 1).' KB';
    $iconColor = match ($extension) {
        'pdf' => 'text-rose-500',
        'doc', 'docx' => 'text-blue-500',
        'xls', 'xlsx', 'csv' => 'text-emerald-500',
        'ppt', 'pptx' => 'text-orange-500',
        'txt', 'md' => 'text-slate-500',
        default => 'text-purple-500',
    };
@endphp

<article
    wire:key="attachment-card-{{ class_basename($attachment) }}-{{ $attachment->id }}"
    class="flex min-w-0 items-center gap-3 rounded-xl border border-slate-200/80 bg-slate-50 p-3 transition-colors hover:border-purple-300 dark:border-[#2f3036] dark:bg-[#16171a] dark:hover:border-purple-700 {{ $compact ? 'max-w-full' : '' }}">
    @if($isImage)
        <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer"
           class="group relative flex h-16 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-[#35363b] dark:bg-[#202126]"
           aria-label="Open {{ $name }}">
            <img src="{{ $previewUrl }}{{ str_contains($previewUrl, '?') ? '&' : '?' }}thumbnail=1"
                 alt="{{ $name }}"
                 loading="lazy"
                 class="h-full w-full object-contain"
                 x-data
                 x-on:error="$el.classList.add('hidden'); $el.nextElementSibling.classList.remove('hidden')">
            <span class="hidden px-1 text-center text-[10px] text-slate-400">Preview unavailable</span>
        </a>
    @else
        <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer"
           class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-[#202126]"
           aria-label="Open {{ $name }}">
            <svg class="h-6 w-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M7 3.75h6.2L19 9.55v10.7A1.75 1.75 0 0117.25 22h-10.5A1.75 1.75 0 015 20.25V5.5a1.75 1.75 0 011.75-1.75zM13 4v6h6M8 14h8m-8 3h8"/>
            </svg>
        </a>
    @endif

    <div class="min-w-0 flex-1">
        <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer"
           class="block truncate text-xs font-semibold text-slate-800 hover:text-purple-700 focus:outline-none focus:underline dark:text-slate-100 dark:hover:text-purple-300"
           title="{{ $name }}">{{ $name }}</a>
        <div class="mt-1 flex min-w-0 flex-wrap items-center gap-x-1.5 text-[10px] text-slate-500 dark:text-slate-400">
            <span>{{ $sizeLabel }}</span>
            @if($attachment->user?->name ?? null)
                <span aria-hidden="true">·</span>
                <span class="max-w-28 truncate">{{ $attachment->user->name }}</span>
            @endif
            @if($attachment->created_at ?? null)
                <span aria-hidden="true">·</span>
                <time datetime="{{ $attachment->created_at->toISOString() }}">{{ $attachment->created_at->diffForHumans() }}</time>
            @endif
        </div>
        @unless($isImage || in_array($mime, ['application/pdf', 'text/plain', 'text/csv'], true))
            <span class="mt-1 block text-[10px] text-slate-500 dark:text-slate-400">Preview may be unavailable; download the original file.</span>
        @endunless
    </div>

    <div class="flex shrink-0 items-center gap-1">
        <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer"
           class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-white hover:text-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:text-slate-400 dark:hover:bg-[#202126] dark:hover:text-purple-300"
           title="Open preview" aria-label="Open preview of {{ $name }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m-1-6L10 14m-4-4H5a2 2 0 00-2 2v7a2 2 0 002 2h7a2 2 0 002-2v-1"/></svg>
        </a>
        <a href="{{ $downloadUrl }}"
           class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-white hover:text-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:text-slate-400 dark:hover:bg-[#202126] dark:hover:text-purple-300"
           title="Download original" aria-label="Download {{ $name }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </a>
        @if($deleteAction)
            <button type="button" wire:click="{{ $deleteAction }}" wire:confirm="Delete this attachment?"
                    wire:loading.attr="disabled"
                    class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-rose-50 hover:text-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-500 disabled:cursor-wait disabled:opacity-50 dark:text-slate-400 dark:hover:bg-rose-950/40 dark:hover:text-rose-300"
                    title="Delete attachment" aria-label="Delete {{ $name }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        @endif
    </div>
</article>
