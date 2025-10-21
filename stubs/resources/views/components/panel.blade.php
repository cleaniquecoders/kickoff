<div {{ $attributes->merge(['class' => 'max-w-full mx-auto overflow-hidden rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm hover:shadow-md transition-shadow duration-200']) }}>
    @isset($header)
        <div class="border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/50 p-4">
            <div class="font-semibold text-gray-900 dark:text-gray-100">
                {{ $header }}
            </div>
        </div>
    @endisset

    <div class="p-4 text-gray-700 dark:text-gray-300">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="bg-gray-50 dark:bg-zinc-900/50 border-t border-gray-200 dark:border-zinc-700 p-4">
            {{ $footer }}
        </div>
    @endisset
</div>
