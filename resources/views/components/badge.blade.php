@props([
    'variant' => 'default',
    'size' => 'md',
])

@php
$variantClasses = [
    'default' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
    'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300',
    'success' => 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300',
    'warning' => 'bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300',
    'danger' => 'bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300',
    'info' => 'bg-info-100 text-info-700 dark:bg-info-900 dark:text-info-300',
];

$sizeClasses = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-sm',
    'lg' => 'px-3 py-1 text-sm',
];

$classes = 'inline-flex items-center gap-1 font-medium rounded-full ' . ($variantClasses[$variant] ?? $variantClasses['default']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
