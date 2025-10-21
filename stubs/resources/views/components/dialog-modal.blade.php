@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-5 bg-white dark:bg-zinc-800">
        <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ $title }}
        </div>

        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-zinc-900/50 text-end border-t border-gray-200 dark:border-zinc-700">
        {{ $footer }}
    </div>
</x-modal>
