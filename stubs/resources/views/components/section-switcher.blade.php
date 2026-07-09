{{-- Sidebar section switcher.

     One dropdown names the active section (Administration, Media, …); the sidebar
     then shows only that section's items. Picking a section navigates
     (wire:navigate) to its landing page. The active section is derived from the
     current route by App\Actions\Builder\Menu\SectionResolver (longest URL-prefix
     match), so deep-links and item clicks always land on the right section with
     no client-side state to keep in sync.

     Sections come from config('menu.sections'); each reuses its menu builder for
     data + authorization, so per-item permission gates and empty-section hiding
     keep working untouched. When no section is authorized (e.g. a user with only
     global items), the whole control renders nothing. --}}
@php
    $resolved = \App\Actions\Builder\Menu\SectionResolver::resolve();
    $sections = $resolved['sections'];
    $active = $resolved['active'];
@endphp

@if ($active)
    {{-- Expanded sidebar: switcher control + the active section's items --}}
    <div class="sidebar-expanded-only">
        <flux:dropdown position="bottom" align="start" class="w-full">
            <button type="button"
                class="mt-2 flex w-full cursor-pointer items-center gap-2.5 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700/60"
                data-tippy-content="{{ __('Switch section') }}">
                <flux:icon :icon="$active['icon']" class="size-4 shrink-0 text-zinc-500 dark:text-zinc-400" />
                <span class="min-w-0 flex-1 truncate text-left">{{ $active['label'] }}</span>
                <flux:icon.chevron-down class="size-4 shrink-0 text-zinc-400" />
            </button>

            <flux:menu class="min-w-56">
                @foreach ($sections as $section)
                    <flux:menu.item icon="{{ $section['icon'] }}" :href="$section['landing']" wire:navigate
                        @class(['font-semibold' => $section['key'] === $active['key']])>
                        {{ $section['label'] }}
                    </flux:menu.item>
                @endforeach
            </flux:menu>
        </flux:dropdown>

        <flux:navlist variant="outline" class="mt-2">
            @include('components.menu.section-items', ['menuItems' => $active['items']])
        </flux:navlist>
    </div>

    {{-- Collapsed rail: each section becomes a flyout icon (reuses the shared
         collapsed template so items/children render exactly as elsewhere). --}}
    <div class="sidebar-collapsed-only">
        <flux:navlist variant="outline">
            @foreach ($sections as $section)
                @include('components.menu.collapsed', [
                    'menu' => null,
                    'menuItems' => $section['items'],
                    'heading' => $section['label'],
                    'headingIcon' => $section['icon'],
                    'hasActiveItem' => $section['owns'],
                ])
            @endforeach
        </flux:navlist>
    </div>
@endif
