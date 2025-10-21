@props(['date', 'format' => false])

@php
    $formattedDate = ($format == 'human')
        ? \Carbon\Carbon::parse($date)->diffForHumans()
        : ($date ? \Carbon\Carbon::parse($date)->format('Y-m-d, H:i') : null);
@endphp

<span {{ $attributes->merge(['class' => 'text-gray-700 dark:text-gray-300']) }}>{{ $formattedDate }}</span>
