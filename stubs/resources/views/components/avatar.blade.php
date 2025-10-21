@props(['name' => 'A', 'size' => 'md'])
@php
    $name = trim(
        collect(explode(' ', $name))
            ->map(function ($segment) {
                return mb_substr($segment, 0, 1);
            })
            ->join(' '),
    );

    $sizeClasses = [
        'sm' => 'h-8 w-8 text-sm',
        'md' => 'h-12 w-12 text-xl',
        'lg' => 'h-16 w-16 text-3xl',
        'xl' => 'h-20 w-20 text-4xl',
    ][$size] ?? 'h-12 w-12 text-xl';
@endphp

<div
    role="img"
    aria-label="{{ auth()->user()?->name ?? 'User avatar' }}"
    {{ $attributes->merge([
        'class' => "bg-gradient-to-br from-indigo-500 to-purple-600 dark:from-indigo-600 dark:to-purple-700 flex items-center justify-center rounded-full font-semibold text-white shadow-lg ring-2 ring-white dark:ring-zinc-800 transition-all duration-300 hover:scale-105 hover:shadow-xl {$sizeClasses}",
    ]) }}>
    {{ $name }}
</div>
