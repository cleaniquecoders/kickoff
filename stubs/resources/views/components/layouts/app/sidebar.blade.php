<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: document.documentElement.classList.contains('dark') }" @dark-mode-changed.window="darkMode = $event.detail.darkMode">

<head>
    @include('partials.head')
</head>

@auth

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        @php
            // Plain cookie (see bootstrap/app.php encryptCookies exception) so the
            // collapsed rail renders server-side under wire:navigate without flicker.
            $sidebarCollapsed = request()->cookie('sidebar_collapsed') === '1';
        @endphp
        <flux:sidebar sticky stashable x-data :data-collapsed="$sidebarCollapsed"
            x-bind:data-collapsed="$store.sidebar.collapsed || false"
            class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <div class="sidebar-header flex items-center justify-between gap-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo />
                </a>

                <button type="button" x-on:click="$store.sidebar.toggle()"
                    class="hidden lg:flex items-center justify-center rounded-lg p-1.5 cursor-pointer text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800 dark:text-white/80 dark:hover:bg-white/[7%] dark:hover:text-white"
                    data-tippy-content="{{ __('Toggle sidebar') }}">
                    <flux:icon.panel-left-close class="size-5 sidebar-expanded-only" />
                    <flux:icon.panel-left-open class="size-5 sidebar-collapsed-only" />
                </button>
            </div>

            <flux:navlist variant="outline">
                <x-menu menu-builder="sidebar" />
                <x-menu menu-builder="media-management" />
                <x-menu menu-builder="administration" />
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <x-menu menu-builder="sidebar-footer" />
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block sidebar-expanded-only" position="bottom" align="start">
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down" />

                <x-user-menu />
            </flux:dropdown>

            <!-- Desktop User Menu (collapsed rail) -->
            <flux:dropdown class="sidebar-collapsed-only" position="bottom" align="start">
                <flux:profile :initials="auth()->user()->initials()" data-tippy-content="{{ auth()->user()->name }}" />

                <x-user-menu />
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile Header -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            {{-- Mobile User Menu --}}
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                <x-user-menu />
            </flux:dropdown>
        </flux:header>

        <x-impersonating />

        {{ $slot }}

        {{-- Toast Notifications --}}
        <x-toast />

        {{-- Convert session messages to toast --}}
        @if (session()->has('message'))
            <script>
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'success',
                        message: @js(session('message'))
                    }
                }));
            </script>
        @endif

        @if (session()->has('error'))
            <script>
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: @js(session('error'))
                    }
                }));
            </script>
        @endif

        @fluxScripts
    </body>
@else

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Authentication Required') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Please log in to access this area.') }}</p>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Log In') }}
                </a>
            </div>
        </div>

        {{-- Toast Notifications --}}
        <x-toast />

        {{-- Convert session messages to toast --}}
        @if (session()->has('message'))
            <script>
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'success',
                        message: @js(session('message'))
                    }
                }));
            </script>
        @endif

        @if (session()->has('error'))
            <script>
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: @js(session('error'))
                    }
                }));
            </script>
        @endif

        @fluxScripts
    </body>
@endauth

</html>
