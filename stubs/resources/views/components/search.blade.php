<div class="w-full flex md:ml-0" action="#" method="GET">
    <label for="search-field" class="sr-only">Search</label>
    <div class="relative w-full text-gray-400 dark:text-gray-500 focus-within:text-indigo-600 dark:focus-within:text-indigo-400">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="h-5 w-5 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        <input id="search" autocomplete="search" name="search"
            class="block w-full h-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 focus:border-transparent transition-all duration-200 sm:text-sm"
            placeholder="Search..." type="search" wire:model.live="keyword" wire:keydown.enter="search">
    </div>
</div>
