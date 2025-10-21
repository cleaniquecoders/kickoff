@props(['menus'])

<nav class="flex-1 px-2 space-y-2">
    @foreach ($menus as $index => $menu)
        @php
            // Skip if menu is not visible
            $isVisible = !isset($menu['visible']) || $menu['visible'] === true;
            if (!$isVisible) {
                continue;
            }

            $label = __($menu['label']);
            $url = $menu['url'];
            $icon = $menu['icon'];
            $type = $menu['type'] ?? 'link';
            $formAttributes = $menu['formAttributes'] ?? [];
            $hasChildren = isset($menu['children']) && count($menu['children']) > 0;

            // Check if any child is active
            $isActive = false;
            if ($hasChildren) {
                foreach ($menu['children'] as $child) {
                    if (request()->url() === $child['url']) {
                        $isActive = true;
                        break;
                    }
                }
            } else {
                $isActive = request()->url() === $menu['url'];
            }

            $iconClass = $isActive
                ? 'text-blue-600 dark:text-blue-400'
                : 'text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400';

            $buttonClass = $isActive
                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800';
        @endphp

        <div class="relative">
            @if($hasChildren)
                <button
                    @click="toggleExpand('menu-{{ $index }}')"
                    @mouseenter="expandedMenu = 'menu-{{ $index }}'"
                    class="{{ $buttonClass }} group flex flex-col items-center justify-center px-2 py-3 text-xs font-medium  rounded-lg transition-all duration-200  w-full"
                >
                    <x-icon name="{{ $icon }}" class="{{ $iconClass }} flex-shrink-0 h-6 w-6 transition-colors duration-200 mb-1">
                    </x-icon>
                    <span class="text-[10px] leading-tight text-center">{{ $label }}</span>

                    @if($isActive)
                        <div class="absolute right-1 top-1 w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></div>
                    @endif
                </button>

                <!-- Flyout Submenu - Full Height -->
                <div
                    x-show="isExpanded('menu-{{ $index }}')"
                    @mouseenter="expandedMenu = 'menu-{{ $index }}'"
                    @mouseleave="expandedMenu = null"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-2"
                    class="fixed left-20 inset-y-0 my-4 ml-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg z-50 overflow-y-auto"
                >
                    <div class="h-full flex flex-col">
                        <!-- Header with menu title -->
                        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-4 z-10">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                                {{ $label }}
                            </h3>
                        </div>

                        <!-- Menu items -->
                        <div class="flex-1 py-2">
                            @foreach ($menu['children'] as $child)
                                @php
                                    // Skip if child menu is not visible
                                    $childIsVisible = !isset($child['visible']) || $child['visible'] === true;
                                    if (!$childIsVisible) {
                                        continue;
                                    }

                                    $childLabel = __($child['label']);
                                    $childUrl = $child['url'];
                                    $childIcon = $child['icon'];
                                    $childType = $child['type'] ?? 'link';
                                    $childFormAttributes = $child['formAttributes'] ?? [];
                                    $childHasChildren = isset($child['children']) && count($child['children']) > 0;
                                    $childIsActive = request()->url() === $child['url'];

                                    // Check if any grandchild is active
                                    if ($childHasChildren) {
                                        foreach ($child['children'] as $grandchild) {
                                            if (request()->url() === $grandchild['url']) {
                                                $childIsActive = true;
                                                break;
                                            }
                                        }
                                    }

                                    $childIconClass = $childIsActive
                                        ? 'text-blue-600 dark:text-blue-400'
                                        : 'text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400';

                                    $childLinkClass = $childIsActive
                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800';
                                @endphp

                                @if($childHasChildren)
                                    <!-- Parent item with children -->
                                    <div class="mx-2 mb-1">
                                        @if($childType === 'form')
                                            <form method="POST" action="{{ $childUrl }}" {!! collect($childFormAttributes)->map(fn($value, $key) => "{$key}=\"{$value}\"")->implode(' ') !!}>
                                                @csrf
                                                <button type="submit"
                                                    class="{{ $childLinkClass }} group flex items-center px-3 py-3 text-sm font-medium transition-all duration-150 rounded-lg w-full text-left"
                                                >
                                                    <x-icon name="{{ $childIcon }}" class="{{ $childIconClass }} mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-200">
                                                    </x-icon>
                                                    <span class="flex-1">{{ $childLabel }}</span>

                                                    @if($childIsActive)
                                                        <div class="w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full"></div>
                                                    @endif
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ $childUrl }}"
                                                class="{{ $childLinkClass }} group flex items-center px-3 py-3 text-sm font-medium transition-all duration-150 rounded-lg"
                                            >
                                                <x-icon name="{{ $childIcon }}" class="{{ $childIconClass }} mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-200">
                                                </x-icon>
                                                <span class="flex-1">{{ $childLabel }}</span>

                                                @if($childIsActive)
                                                    <div class="w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full"></div>
                                                @endif
                                            </a>
                                        @endif

                                        <!-- Nested children (grandchildren) - no icons, just labels -->
                                        <div class="mt-1 ml-8 space-y-1">
                                            @foreach ($child['children'] as $grandchild)
                                                @php
                                                    // Skip if grandchild menu is not visible
                                                    $grandchildIsVisible = !isset($grandchild['visible']) || $grandchild['visible'] === true;
                                                    if (!$grandchildIsVisible) {
                                                        continue;
                                                    }

                                                    $grandchildLabel = __($grandchild['label']);
                                                    $grandchildUrl = $grandchild['url'];
                                                    $grandchildType = $grandchild['type'] ?? 'link';
                                                    $grandchildFormAttributes = $grandchild['formAttributes'] ?? [];
                                                    $grandchildIsActive = request()->url() === $grandchild['url'];

                                                    $grandchildLinkClass = $grandchildIsActive
                                                        ? 'text-blue-700 dark:text-blue-300 font-medium'
                                                        : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400';
                                                @endphp

                                                @if($grandchildType === 'form')
                                                    <form method="POST" action="{{ $grandchildUrl }}" {!! collect($grandchildFormAttributes)->map(fn($value, $key) => "{$key}=\"{$value}\"")->implode(' ') !!}>
                                                        @csrf
                                                        <button type="submit"
                                                            class="{{ $grandchildLinkClass }} flex items-center px-3 py-2 text-xs transition-all duration-150 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 w-full text-left"
                                                        >
                                                            <span class="flex-1">{{ $grandchildLabel }}</span>

                                                            @if($grandchildIsActive)
                                                                <div class="w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full"></div>
                                                            @endif
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ $grandchildUrl }}"
                                                        class="{{ $grandchildLinkClass }} flex items-center px-3 py-2 text-xs transition-all duration-150 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800"
                                                    >
                                                        <span class="flex-1">{{ $grandchildLabel }}</span>

                                                        @if($grandchildIsActive)
                                                            <div class="w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full"></div>
                                                        @endif
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Simple child item without children -->
                                    @if($childType === 'form')
                                        <form method="POST" action="{{ $childUrl }}" class="mx-2 mb-1" {!! collect($childFormAttributes)->map(fn($value, $key) => "{$key}=\"{$value}\"")->implode(' ') !!}>
                                            @csrf
                                            <button type="submit"
                                                class="{{ $childLinkClass }} group flex items-center px-3 py-3 text-sm font-medium transition-all duration-150 rounded-lg w-full text-left"
                                            >
                                                <x-icon name="{{ $childIcon }}" class="{{ $childIconClass }} mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-200">
                                                </x-icon>
                                                <span class="flex-1">{{ $childLabel }}</span>

                                                @if($childIsActive)
                                                    <div class="w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full"></div>
                                                @endif
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ $childUrl }}"
                                            class="{{ $childLinkClass }} group flex items-center mx-2 px-3 py-3 text-sm font-medium transition-all duration-150 rounded-lg mb-1"
                                        >
                                            <x-icon name="{{ $childIcon }}" class="{{ $childIconClass }} mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-200">
                                            </x-icon>
                                            <span class="flex-1">{{ $childLabel }}</span>

                                            @if($childIsActive)
                                                <div class="w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full"></div>
                                            @endif
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                @if($type === 'form')
                    <form method="POST" action="{{ $url }}" {!! collect($formAttributes)->map(fn($value, $key) => "{$key}=\"{$value}\"")->implode(' ') !!}>
                        @csrf
                        <button type="submit"
                            class="{{ $buttonClass }} group flex flex-col items-center justify-center px-2 py-3 text-xs font-medium  rounded-lg transition-all duration-200 w-full"
                        >
                            <x-icon name="{{ $icon }}" class="{{ $iconClass }} flex-shrink-0 h-6 w-6 transition-colors duration-200 mb-1">
                            </x-icon>
                            <span class="text-[10px] leading-tight text-center">{{ $label }}</span>

                            @if($isActive)
                                <div class="absolute right-1 top-1 w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></div>
                            @endif
                        </button>
                    </form>
                @else
                    <a href="{{ $url }}"
                        class="{{ $buttonClass }} group flex flex-col items-center justify-center px-2 py-3 text-xs font-medium  rounded-lg transition-all duration-200 "
                    >
                        <x-icon name="{{ $icon }}" class="{{ $iconClass }} flex-shrink-0 h-6 w-6 transition-colors duration-200 mb-1">
                        </x-icon>
                        <span class="text-[10px] leading-tight text-center">{{ $label }}</span>

                        @if($isActive)
                            <div class="absolute right-1 top-1 w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></div>
                        @endif
                    </a>
                @endif
            @endif
        </div>
    @endforeach
</nav>
