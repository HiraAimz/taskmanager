@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            {{ $header }}
        </div>
    @endif
    
    <div @class(['px-6 py-4' => $padding])>
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 rounded-b-xl">
            {{ $footer }}
        </div>
    @endif
</div>
