<x-layouts.app.sidebar :title="$title ?? null" :description="$description ?? null">
    {{-- `container` gives max-w-7xl + mx-auto + p-6 lg:p-8. Pages render content
         directly here — do NOT re-wrap in another max-w-7xl/px-* div (double padding). --}}
    <flux:main container>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
