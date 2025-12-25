<div class="flex items-center space-x-3 px-2">
    {{-- Kickoff Icon --}}
    <div class="flex-shrink-0">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-gradient-to-br dark:from-blue-500 dark:to-blue-600 border-2 border-white shadow-sm">
            <x-lucide-rocket class="h-6 w-6 text-blue-600 dark:text-white" />
        </div>
    </div>

    {{-- App Name --}}
    <div class="flex flex-col">
        <span class="text-base font-bold text-zinc-900 dark:text-white">
            {{ config('app.name') }}
        </span>
        <span class="text-xs text-zinc-500 dark:text-zinc-400">
            Powered by Kickoff
        </span>
    </div>
</div>
