@props([
    'label' => null,
    'name',
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'helper' => null,
    'error' => null,
])

<div>
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input
                type="checkbox"
                name="{{ $name }}"
                id="{{ $name }}"
                value="{{ $value }}"
                @if($checked) checked @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge([
                    'class' => 'h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed' . ($error ? ' border-danger-300 focus:ring-danger-500' : '')
                ]) }}
            >
        </div>
        @if($label)
            <div class="ml-2">
                <label for="{{ $name }}" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    {{ $label }}
                </label>
                @if($helper)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $helper }}</p>
                @endif
            </div>
        @endif
    </div>
    
    @if($error)
        <p class="mt-1.5 text-xs text-danger-600 dark:text-danger-400 flex items-center gap-1 ml-6">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
