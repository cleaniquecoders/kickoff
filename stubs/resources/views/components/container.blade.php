<div>
    <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div {{ $attributes->merge(['class' => 'bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6 hover:shadow-md transition-shadow duration-200']) }}>
            {{ $slot }}
        </div>
    </div>
</div>
