@props(['type' => 'created', 'label' => 'Created'])
@php
    if(isset($row)) {
        $type = $row->event;
        $label = strtoupper($row->event);
    }

    $styles = match($type) {
        'created' => 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/30',
        'updated' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/30',
        'deleted' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/30',
        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/30',
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/30',
        'warning' => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-500/10 dark:text-orange-400 dark:ring-orange-500/30',
        'info' => 'bg-cyan-50 text-cyan-700 ring-cyan-600/20 dark:bg-cyan-500/10 dark:text-cyan-400 dark:ring-cyan-500/30',
        default => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/30',
    };

    $dotColor = match($type) {
        'created' => 'bg-green-500 dark:bg-green-400',
        'updated' => 'bg-blue-500 dark:bg-blue-400',
        'deleted' => 'bg-red-500 dark:bg-red-400',
        'pending' => 'bg-yellow-500 dark:bg-yellow-400',
        'success' => 'bg-emerald-500 dark:bg-emerald-400',
        'warning' => 'bg-orange-500 dark:bg-orange-400',
        'info' => 'bg-cyan-500 dark:bg-cyan-400',
        default => 'bg-gray-500 dark:bg-gray-400',
    };
@endphp

<span class="inline-flex items-center gap-x-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset transition-all duration-200 {{ $styles }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $dotColor }} animate-pulse"></span>
    {{ $label ?? '' }}
</span>
