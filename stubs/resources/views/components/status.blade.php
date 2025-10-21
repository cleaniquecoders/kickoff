@props([
    'tooltip' => 'Status',
    'condition' => false,
    'okIcon' => 'o-check',
    'okClass' => 'text-emerald-600 border-emerald-500 bg-emerald-50 dark:text-emerald-400 dark:border-emerald-500 dark:bg-emerald-500/10',
    'falseIcon' => 'o-x',
    'falseClass' => 'text-red-600 border-red-500 bg-red-50 dark:text-red-400 dark:border-red-500 dark:bg-red-500/10',
    'okLabel' => 'Active',
    'falseLabel' => 'Inactive',
    'labelClass' => 'ml-2 text-sm font-medium',
])
<div class="inline-flex items-center gap-2">
    <span class="relative flex h-5 w-5 items-center justify-center rounded-full border-2 transition-all duration-200 {{ $condition ? $okClass : $falseClass }}">
        <x-icon name="{{ $condition ? $okIcon : $falseIcon }}" class="h-3 w-3" />
    </span>
    <span class="{{ $labelClass }} text-gray-900 dark:text-gray-100">{{ $condition ? $okLabel : $falseLabel }}</span>
</div>
