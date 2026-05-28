@props([
    'title',
    'subtitle' => null,
    'open' => false,
])
{{-- Reusable accordion item. Each instance owns its own open/closed
     Alpine state but listens for window-level "accordion-expand-all"
     and "accordion-collapse-all" events so a sibling toolbar button
     can drive every item at once. CSS-driven height animation via
     grid-template-rows 0fr ↔ 1fr so content height is content-driven,
     not hard-coded. --}}
@once
    <style>
        .accordion-body {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 320ms ease-out, margin-top 320ms ease-out;
            margin-top: 0;
        }
        .accordion-body > div { overflow: hidden; }
        .accordion-item.is-open .accordion-body {
            grid-template-rows: 1fr;
            margin-top: 1rem;
        }
        .accordion-chevron { transition: transform 320ms ease-out; }
        .accordion-item.is-open .accordion-chevron { transform: rotate(90deg); }
        @media (prefers-reduced-motion: reduce) {
            .accordion-body, .accordion-chevron { transition: none; }
        }
    </style>
@endonce

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }"
     x-on:accordion-expand-all.window="open = true"
     x-on:accordion-collapse-all.window="open = false"
     :class="open ? 'is-open border-zinc-300 dark:border-zinc-600' : ''"
     class="accordion-item rounded-lg border border-zinc-200 bg-white p-4 transition-colors dark:border-zinc-800 dark:bg-zinc-900">
    <button type="button" @click="open = !open"
            :aria-expanded="open"
            class="flex w-full cursor-pointer items-center justify-between gap-4 text-left">
        <div class="min-w-0">
            <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $title }}</div>
            @if ($subtitle)
                <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</div>
            @endif
        </div>
        <flux:icon.chevron-right class="accordion-chevron h-5 w-5 flex-shrink-0 text-zinc-400" />
    </button>
    <div class="accordion-body">
        <div class="pt-1">
            {{ $slot }}
        </div>
    </div>
</div>
