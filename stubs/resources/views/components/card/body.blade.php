@props([
    'padding' => true,
])

@php
$classes = $padding
    ? 'px-6 py-4'
    : '';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
