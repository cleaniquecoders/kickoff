@php
$hasNotification = auth()
    ->user()
    ->hasNotifications();
@endphp

<div class="relative group">
    @if ($hasNotification)
        <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-red-500 dark:bg-red-400 ring-2 ring-white dark:ring-zinc-800 animate-pulse"></div>
    @endif
    <x-icon name="o-bell" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200 w-6 h-6"></x-icon>
</div>
