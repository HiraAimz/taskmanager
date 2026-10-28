@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'helper' => null,
    'error' => null,
    'icon' => null,
])

@php
    $inputId = $id ?? $name ?? 'input-' . uniqid();
    $inputName = $name ?? $inputId;
@endphp

<div>
    @if($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                {{ $icon }}
            </div>
        @endif
        
        <input
            type="{{ $type }}"
            name="{{ $inputName }}"
            id="{{ $inputId }}"
            value="{{ old($inputName, $value) }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge([
                'class' => 'block w-full text-xs font-semibold rounded-xl border border-slate-200 dark:border-[#323338] bg-white dark:bg-[#16171a] text-slate-900 dark:text-white placeholder-slate-400 py-2.5 ' . ($icon ? 'pl-10 pr-3.5' : 'px-3.5') . ' focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed ' . ($error ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20' : '')
            ]) }}
        >
    </div>
    
    @if($helper && !$error)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $helper }}</p>
    @endif
    
    @if($error)
        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
