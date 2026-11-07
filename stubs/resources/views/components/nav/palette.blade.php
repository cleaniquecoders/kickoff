{{-- Cmd/Ctrl-K: every page this user may open, built from the same menu builders as the
     sidebar. Fed as JSON in a script tag: a double quote inside x-data would end the attribute. --}}
@props(['pages'])

<script type="application/json" id="nav-palette-pages">@json(array_values($pages))</script>

<div x-data="navPalette" x-on:keydown.window="globalKey($event)" x-on:nav-palette-open.window="show($event.detail)">
    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-[12vh]"
            x-on:keydown.escape.prevent.stop="close()">
            <div class="absolute inset-0 bg-zinc-950/40 motion-safe:transition-opacity" x-on:click="close()" aria-hidden="true"></div>

            <div role="dialog" aria-modal="true" aria-label="{{ __('Search pages') }}" x-trap.noscroll="open"
                class="relative w-full max-w-lg overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-white/10 dark:bg-zinc-900">
                <div class="flex items-center gap-2 border-b border-zinc-200 px-3 dark:border-white/10">
                    <flux:icon.search class="size-4 shrink-0 text-zinc-500 dark:text-zinc-400" aria-hidden="true" />
                    <input x-ref="input" x-model="query" type="text" role="combobox" autocomplete="off" spellcheck="false"
                        aria-expanded="true" aria-controls="nav-palette-list" aria-autocomplete="list"
                        x-bind:aria-activedescendant="results.length ? 'nav-palette-opt-' + active : null"
                        x-on:keydown.arrow-down.prevent="move(1)" x-on:keydown.arrow-up.prevent="move(-1)"
                        x-on:keydown.enter.prevent="go()" x-on:input="active = 0"
                        placeholder="{{ __('Search pages…') }}" aria-label="{{ __('Search pages') }}"
                        class="h-12 min-w-0 flex-1 border-0 bg-transparent text-base text-zinc-900 placeholder-zinc-500 focus:outline-none focus:ring-0 dark:text-white dark:placeholder-zinc-400 sm:text-sm">
                    <kbd class="hidden rounded border border-zinc-300 px-1.5 py-0.5 font-mono text-[11px] text-zinc-500 sm:inline dark:border-white/15 dark:text-zinc-400">Esc</kbd>
                </div>

                <ul id="nav-palette-list" role="listbox" aria-label="{{ __('Pages') }}" class="max-h-[50vh] overflow-y-auto p-2">
                    <template x-for="(page, i) in results" x-bind:key="page.url">
                        <li role="option" x-bind:id="'nav-palette-opt-' + i" x-bind:aria-selected="i === active ? 'true' : 'false'"
                            x-on:mousemove="active = i" x-on:click="go(i)"
                            class="flex min-h-11 cursor-pointer items-center gap-3 rounded-lg px-3 py-2 text-sm text-zinc-700 aria-selected:bg-zinc-100 aria-selected:text-zinc-900 dark:text-zinc-300 dark:aria-selected:bg-white/10 dark:aria-selected:text-white">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-medium" x-text="page.label"></span>
                                <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400" x-text="page.context"></span>
                            </span>
                        </li>
                    </template>
                </ul>

                <p x-show="query !== '' && results.length === 0" class="px-4 pb-6 pt-2 text-center text-sm text-zinc-600 dark:text-zinc-300">
                    {{ __('No page matches') }} <span class="font-medium" x-text="'“' + query + '”'"></span>
                </p>

                <p class="sr-only" role="status" aria-live="polite"><span x-text="results.length"></span> {{ __('pages') }}</p>
            </div>
        </div>
    </template>
</div>
