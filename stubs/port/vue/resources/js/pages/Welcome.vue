<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Welcome" />

<!--
TODO (12):
 - @auth: auth check — use shared `auth.user` Inertia prop
 - @include: blade @include — extract to a shared component or inline
 - x-kickoff-logo blade component — port to kit equivalent or drop
 - x-lucide-sparkles blade component — port to kit equivalent or drop
 - x-lucide-arrow-right blade component — port to kit equivalent or drop
 - x-lucide-shield-check blade component — port to kit equivalent or drop
 - x-lucide-users blade component — port to kit equivalent or drop
 - x-lucide-activity blade component — port to kit equivalent or drop
 - x-lucide-zap blade component — port to kit equivalent or drop
 - x-lucide-database blade component — port to kit equivalent or drop
 - x-lucide-palette blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>

    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-900">

        
        <nav class="fixed top-0 z-50 w-full border-b border-zinc-200 bg-white/80 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-900/80">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <!-- TODO: x-kickoff-logo -->
                        <span class="text-lg font-bold text-zinc-900 dark:text-white">{{ config('app.name') }}</span>
                    </a>

                    
                    <div class="flex items-center space-x-4">

                            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
                                {{ __('Dashboard') }}
                            </a>

                            <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
                                {{ __('Log in') }}
                            </a>

                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                                    {{ __('Get Started') }}
                                </a>

                    </div>
                </div>
            </div>
        </nav>

        
        <section class="relative overflow-hidden pt-32 pb-20 sm:pt-40 sm:pb-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    
                    <div class="mb-6 inline-flex items-center rounded-full border border-zinc-200 bg-zinc-50 px-4 py-1.5 dark:border-zinc-700 dark:bg-zinc-800">
                        <!-- TODO: x-lucide-sparkles -->
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ __('Powered by Kickoff') }}</span>
                    </div>

                    
                    <h1 class="text-4xl font-bold tracking-tight text-zinc-900 sm:text-6xl dark:text-white">
                        {{ __('Build something') }}
                        <span class="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">{{ __('amazing') }}</span>
                    </h1>

                    
                    <p class="mx-auto mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                        {{ __('A modern Laravel application bootstrapped with best practices, pre-configured packages, and a solid foundation for your next project.') }}
                    </p>

                    
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">

                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                                {{ __('Go to Dashboard') }}
                                <!-- TODO: x-lucide-arrow-right -->
                            </a>

                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                                    {{ __('Get Started Free') }}
                                    <!-- TODO: x-lucide-arrow-right -->
                                </a>

                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg border border-zinc-300 bg-white px-6 py-3 text-base font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 dark:focus:ring-offset-zinc-900">
                                {{ __('Sign In') }}
                            </a>

                    </div>
                </div>
            </div>

            
            <div class="absolute inset-0 -z-10 overflow-hidden">
                <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2">
                    <div class="h-[500px] w-[500px] rounded-full bg-gradient-to-br from-blue-400/20 to-cyan-400/20 blur-3xl dark:from-blue-600/10 dark:to-cyan-600/10"></div>
                </div>
            </div>
        </section>

        
        <section class="py-20 sm:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
                        {{ __('Everything you need to get started') }}
                    </h2>
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                        {{ __('Built with modern tools and best practices to help you ship faster.') }}
                    </p>
                </div>

                <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    
                    <div class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
                        <div class="mb-4 inline-flex rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                            <!-- TODO: x-lucide-shield-check -->
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Authentication Ready') }}</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('Complete authentication system with login, registration, password reset, and email verification.') }}
                        </p>
                    </div>

                    
                    <div class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
                        <div class="mb-4 inline-flex rounded-lg bg-green-100 p-3 dark:bg-green-900/50">
                            <!-- TODO: x-lucide-users -->
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Role & Permissions') }}</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('Flexible role-based access control powered by Spatie Laravel Permission.') }}
                        </p>
                    </div>

                    
                    <div class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
                        <div class="mb-4 inline-flex rounded-lg bg-purple-100 p-3 dark:bg-purple-900/50">
                            <!-- TODO: x-lucide-activity -->
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Activity Logging') }}</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('Track all user activities with comprehensive audit trail using Spatie Activity Log.') }}
                        </p>
                    </div>

                    
                    <div class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
                        <div class="mb-4 inline-flex rounded-lg bg-amber-100 p-3 dark:bg-amber-900/50">
                            <!-- TODO: x-lucide-zap -->
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Livewire Powered') }}</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('Interactive UI components built with Livewire 4 and Flux for a modern experience.') }}
                        </p>
                    </div>

                    
                    <div class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
                        <div class="mb-4 inline-flex rounded-lg bg-cyan-100 p-3 dark:bg-cyan-900/50">
                            <!-- TODO: x-lucide-database -->
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Media Management') }}</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('File upload support with Spatie Media Library, ready for S3 and cloud storage. UI coming soon.') }}
                        </p>
                    </div>

                    
                    <div class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
                        <div class="mb-4 inline-flex rounded-lg bg-rose-100 p-3 dark:bg-rose-900/50">
                            <!-- TODO: x-lucide-palette -->
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Dark Mode') }}</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('Beautiful light and dark themes with smooth transitions and system preference detection.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="border-t border-zinc-200 bg-zinc-50 py-16 dark:border-zinc-800 dark:bg-zinc-800/30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    {{ __('Built with') }}
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
                    <span class="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Laravel</span>
                    <span class="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Livewire</span>
                    <span class="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Tailwind CSS</span>
                    <span class="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Alpine.js</span>
                    <span class="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Flux</span>
                </div>
            </div>
        </section>

        
        <footer class="border-t border-zinc-200 py-12 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center space-x-2">
                        <!-- TODO: x-kickoff-logo -->
                        <span class="font-semibold text-zinc-900 dark:text-white">{{ config('app.name') }}</span>
                    </div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
                    </p>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
</template>