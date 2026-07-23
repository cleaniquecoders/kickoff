<x-layouts.app.sidebar :title="$title ?? null" :description="$description ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
