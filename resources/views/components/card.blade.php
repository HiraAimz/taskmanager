@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] shadow-xs']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-slate-100 dark:border-[#25262c]">
            {{ $header }}
        </div>
    @endif
    
    <div @class(['p-6' => $padding])>
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="px-6 py-4 border-t border-slate-100 dark:border-[#25262c] bg-slate-50 dark:bg-[#16171a] rounded-b-2xl">
            {{ $footer }}
        </div>
    @endif
</div>
