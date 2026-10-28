@props([
    'label' => null,
    'name',
    'id' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'helper' => null,
    'error' => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<div>
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input
                type="checkbox"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="{{ $value }}"
                @if($checked) checked @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge([
                    'class' => 'h-4 w-4 rounded border-slate-300 dark:border-[#323338] text-purple-600 focus:ring-purple-500/20 accent-purple-600 dark:bg-[#1a1b1e] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer' . ($error ? ' border-rose-400 focus:ring-rose-500' : '')
                ]) }}
            >
        </div>
        @if($label)
            <div class="ml-2.5">
                <label for="{{ $inputId }}" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                    {{ $label }}
                </label>
                @if($helper)
                    <p class="text-2xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $helper }}</p>
                @endif
            </div>
        @endif
    </div>
    
    @if($error)
        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1 ml-6">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
