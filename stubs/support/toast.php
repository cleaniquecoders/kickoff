<?php

declare(strict_types=1);

if (! function_exists('toast')) {
    /**
     * Flash a toast notification to the session.
     *
     * Use in controllers/middleware. In Livewire components,
     * use $this->dispatch('toast', type: '...', message: '...') instead.
     *
     * @param  string  $type  'success', 'error', 'warning', 'info'
     * @param  int  $duration  Duration in milliseconds
     */
    function toast(string $message, string $type = 'success', int $duration = 3000): void
    {
        session()->flash('toast', [
            'message' => $message,
            'type' => $type,
            'duration' => $duration,
        ]);
    }
}
