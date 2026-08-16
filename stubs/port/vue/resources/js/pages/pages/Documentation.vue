<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Documentation" />

<!--
TODO (4):
 - x-layouts.app blade component — port to kit equivalent or drop
 - x-breadcrumbs blade component — port to kit equivalent or drop
 - x-icon blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!-- TODO: x-layouts.app -->
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <!-- TODO: x-breadcrumbs -->

        <div class="space-y-2">
            <Heading size="xl" level="1">
                <!-- TODO: x-icon -->
                {{ __('Documentation') }}
            </Heading>
            <Text>
                {{ __('Comprehensive guides and documentation for using the application') }}
            </Text>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Getting Started -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Getting Started') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Learn the basics and get up and running quickly with our step-by-step guide.') }}
                </p>
            </div>

            <!-- User Guide -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('User Guide') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Detailed instructions on how to use all features of the application.') }}
                </p>
            </div>

            <!-- API Reference -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-purple-100 p-2 dark:bg-purple-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('API Reference') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Complete API documentation for developers and integrators.') }}
                </p>
            </div>

            <!-- Best Practices -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-yellow-100 p-2 dark:bg-yellow-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Best Practices') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Tips and tricks to get the most out of the application.') }}
                </p>
            </div>

            <!-- Troubleshooting -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-red-100 p-2 dark:bg-red-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Troubleshooting') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Common issues and solutions to help you resolve problems quickly.') }}
                </p>
            </div>

            <!-- FAQs -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-indigo-100 p-2 dark:bg-indigo-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('FAQs') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Frequently asked questions and answers from our community.') }}
                </p>
            </div>
        </div>

        <!-- Additional Resources -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">{{ __('Additional Resources') }}</h2>
            <div class="space-y-3">
                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <!-- TODO: x-icon -->
                    <span>{{ __('Video Tutorials') }}</span>
                </a>
                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <!-- TODO: x-icon -->
                    <span>{{ __('Community Forum') }}</span>
                </a>
                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <!-- TODO: x-icon -->
                    <span>{{ __('Blog Articles') }}</span>
                </a>
            </div>
        </div>
    </div>
</template>