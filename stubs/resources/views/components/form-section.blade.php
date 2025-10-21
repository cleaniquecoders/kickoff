@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-4 py-5 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 sm:p-6 shadow-sm {{ isset($actions) ? 'sm:rounded-t-xl' : 'sm:rounded-xl' }}">
                <div class="grid grid-cols-6 gap-6">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-zinc-900/50 text-end sm:px-6 border border-t-0 border-gray-200 dark:border-zinc-700 shadow-sm sm:rounded-b-xl gap-3">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
