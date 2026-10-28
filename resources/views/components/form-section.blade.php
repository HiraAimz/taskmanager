@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
        <h3 class="text-card-title text-slate-900 dark:text-slate-100">{{ $title }}</h3>
        @if($description)
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $description }}</p>
        @endif
    </div>
    
    <div class="space-y-4">
        {{ $slot }}
    </div>
</div>
