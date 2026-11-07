{{-- One area's panel: docked beside the rail, as an overlay, or inline in the mobile
     drawer. `pin` draws the header with the keep-open control. --}}
@props(['area', 'touch' => false, 'pin' => false])

<div class="flex min-h-0 flex-1 flex-col">
    @if ($pin)
        <div class="flex h-14 shrink-0 items-center justify-between gap-2 px-4">
            <h2 class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $area['heading'] }}</h2>
            <button type="button" x-on:click="togglePin()"
                class="flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-md text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800 focus-visible:outline-2 focus-visible:outline-accent dark:text-zinc-400 dark:hover:bg-white/[7%] dark:hover:text-white"
                x-bind:aria-pressed="docked ? 'true' : 'false'"
                aria-label="{{ __('Keep panel open') }}" title="{{ __('Keep panel open ([)') }}">
                <flux:icon.pin class="size-4" x-show="! docked" />
                <flux:icon.pin-off class="size-4" x-show="docked" x-cloak />
            </button>
        </div>
    @endif

    <div class="min-h-0 flex-1 overflow-y-auto px-2 pb-3">
        <flux:navlist variant="outline" :aria-label="$area['heading']">
            <x-nav.panel-items :items="$area['items']" :touch="$touch" />
        </flux:navlist>
    </div>
</div>
