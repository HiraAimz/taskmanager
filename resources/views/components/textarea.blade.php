@props([
    'label' => null,
    'name',
    'value' => '',
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'helper' => null,
    'error' => null,
    'maxlength' => null,
    'showCounter' => false,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed text-sm resize-none' . ($error ? ' border-danger-300 focus:border-danger-500 focus:ring-danger-500' : '')
            ]) }}
        >{{ old($name, $value) }}</textarea>
        
        @if($showCounter && $maxlength)
            <div class="absolute bottom-2 right-2 text-xs text-slate-400 bg-white dark:bg-slate-800 px-1">
                <span x-data="{ count: $el.parentElement.previousElementSibling.value.length }" x-text="count"></span>/<span>{{ $maxlength }}</span>
            </div>
        @endif
    </div>
    
    @if($helper && !$error)
        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ $helper }}</p>
    @endif
    
    @if($error)
        <p class="mt-1.5 text-xs text-danger-600 dark:text-danger-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
