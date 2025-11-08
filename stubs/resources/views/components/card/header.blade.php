@props([
    'padding' => true,
])

@php
$classes = $padding
    ? 'border-b border-zinc-200 bg-zinc-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800/50'
    : 'border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/50';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
