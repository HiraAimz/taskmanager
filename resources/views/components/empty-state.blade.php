@props([
    'title',
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12']) }}>
    @if($icon)
        <div class="mx-auto h-12 w-12 text-slate-400 mb-4">
            {{ $icon }}
        </div>
    @endif
    
    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-1">{{ $title }}</h3>
    
    @if($description)
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">{{ $description }}</p>
    @endif
    
    @if(isset($action))
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>
