<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: document.documentElement.classList.contains('dark') }" @dark-mode-changed.window="darkMode = $event.detail.darkMode">

<head>
    @include('partials.head')
</head>

@auth

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        @include('components.breadcrumbs')
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <x-menu menu-builder="sidebar" />
                <x-menu menu-builder="user-management" />
                <x-menu menu-builder="settings" />
                <x-menu menu-builder="audit-monitoring" />
            </flux:navlist>

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down" />

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
