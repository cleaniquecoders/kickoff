<div class="flex items-center space-x-3 px-2">
    {{-- Kickoff Logo --}}
    <div class="flex-shrink-0">
        <x-kickoff-logo class="h-10 w-10" />
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
