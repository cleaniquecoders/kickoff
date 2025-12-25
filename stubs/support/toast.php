<?php

if (! function_exists('toast')) {
    /**
     * Dispatch a toast notification.
     *
     * @param  string  $type  'success', 'error', 'warning', 'info'
     * @param  int  $duration  Duration in milliseconds
     */
    function toast(string $message, string $type = 'success', int $duration = 3000): void
    {
        // This will be used in Livewire components
        // In regular views, it won't do anything
    }
}
