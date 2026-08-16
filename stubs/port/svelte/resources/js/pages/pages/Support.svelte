<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Support</title></svelte:head>

<AppLayout>

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
                {__('Support')}
            </Heading>
            <Text>
                {__('Get help and support for any issues you\'re experiencing')}
            </Text>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Contact Support -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{__('Contact Support')}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {__('Reach out to our support team for personalized assistance.')}
                </p>
                <a href="mailto:support@example.com" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    <!-- TODO: x-icon -->
                    {__('Email Support')}
                </a>
            </div>

            <!-- Live Chat -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{__('Live Chat')}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {__('Get instant help from our support team during business hours.')}
                </p>
                <button type="button" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                    <!-- TODO: x-icon -->
                    {__('Start Chat')}
                </button>
            </div>

            <!-- Knowledge Base -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-purple-100 p-2 dark:bg-purple-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{__('Knowledge Base')}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {__('Browse our extensive library of help articles and guides.')}
                </p>
                <a href="#" class="inline-flex items-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600">
                    <!-- TODO: x-icon -->
                    {__('Browse Articles')}
                </a>
            </div>

            <!-- Community Forum -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-yellow-100 p-2 dark:bg-yellow-900">
                        <!-- TODO: x-icon -->
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{__('Community Forum')}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {__('Connect with other users and share tips and solutions.')}
                </p>
                <a href="#" class="inline-flex items-center gap-2 rounded-md bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600">
                    <!-- TODO: x-icon -->
                    {__('Visit Forum')}
                </a>
            </div>
        </div>

        <!-- Support Hours -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">{__('Support Hours')}</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <h4 class="mb-2 font-medium text-gray-900 dark:text-white">{__('Email Support')}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{__('24/7 - Response within 24 hours')}</p>
                </div>
                <div>
                    <h4 class="mb-2 font-medium text-gray-900 dark:text-white">{__('Live Chat')}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{__('Monday - Friday, 9:00 AM - 5:00 PM UTC')}</p>
                </div>
            </div>
        </div>

        <!-- Submit a Ticket -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">{__('Submit a Support Ticket')}</h2>
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                {__('Can\'t find what you\'re looking for? Submit a support ticket and we\'ll get back to you as soon as possible.')}
            </p>
            <button type="button" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                <!-- TODO: x-icon -->
                {__('Create Ticket')}
            </button>
        </div>
    </div>
</AppLayout>