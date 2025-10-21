@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-500 dark:border-indigo-400 text-sm font-semibold leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-indigo-700 dark:focus:border-indigo-300 transition-all duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:border-gray-300 dark:hover:border-zinc-600 focus:outline-none focus:text-gray-900 dark:focus:text-gray-100 focus:border-gray-300 dark:focus:border-zinc-600 transition-all duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
