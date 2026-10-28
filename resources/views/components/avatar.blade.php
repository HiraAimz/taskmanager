@props([
    'name',
    'image' => null,
    'size' => 'md',
])

@php
$sizeClasses = [
    'xs' => 'w-6 h-6 text-xs',
    'sm' => 'w-8 h-8 text-sm',
    'md' => 'w-10 h-10 text-base',
    'lg' => 'w-12 h-12 text-lg',
    'xl' => 'w-16 h-16 text-xl',
];

$initial = strtoupper(substr($name, 0, 1));
$colors = [
    'bg-primary-500',
    'bg-success-500',
    'bg-warning-500',
    'bg-danger-500',
    'bg-info-500',
    'bg-purple-500',
    'bg-pink-500',
    'bg-indigo-500',
];
$colorIndex = ord($initial) % count($colors);
$bgColor = $colors[$colorIndex];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center justify-center font-semibold text-white rounded-full flex-shrink-0 ' . $bgColor . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md'])]) }}>
    @if($image)
        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full rounded-full object-cover">
    @else
        {{ $initial }}
    @endif
</div>
