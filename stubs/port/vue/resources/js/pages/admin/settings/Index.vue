<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Index" />

<!--
TODO (11):
 - x-layouts.app blade component — port to kit equivalent or drop
 - x-card blade component — port to kit equivalent or drop
 - x-card.header blade component — port to kit equivalent or drop
 - x-lucide-globe blade component — port to kit equivalent or drop
 - x-card.body blade component — port to kit equivalent or drop
 - x-card.footer blade component — port to kit equivalent or drop
 - x-lucide-lock-keyhole blade component — port to kit equivalent or drop
 - x-lucide-life-buoy blade component — port to kit equivalent or drop
 - x-lucide-mail blade component — port to kit equivalent or drop
 - x-lucide-search blade component — port to kit equivalent or drop
 - x-lucide-bell blade component — port to kit equivalent or drop
-->

<!-- TODO: x-layouts.app -->
    <Heading size="xl" class="mb-6">Application Settings</Heading>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-globe -->
                    <Heading size="lg">General</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Manage your site name and general application settings.
                </p>
            
            <!-- TODO: x-card.footer -->
                <Button variant="primary" :href="route('admin.settings.show', 'general')">
                    Configure
                </Button>
            
        

        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-lock-keyhole -->
                    <Heading size="lg">Authentication</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Control how users sign in and whether they can register themselves.
                </p>
            
            <!-- TODO: x-card.footer -->
                <Button variant="primary" :href="route('admin.settings.authentication')">
                    Configure
                </Button>
            
        

        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-life-buoy -->
                    <Heading size="lg">Support</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Embed the g8desk support widget for your users.
                </p>
            
            <!-- TODO: x-card.footer -->
                <Button variant="primary" :href="route('admin.settings.g8desk')">
                    Configure
                </Button>
            
        

        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-mail -->
                    <Heading size="lg">Email</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Configure the default sender address and name for outgoing emails.
                </p>
            
            <!-- TODO: x-card.footer -->
                <Button variant="primary" :href="route('admin.settings.show', 'email')">
                    Configure
                </Button>
            
        

        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-search -->
                    <Heading size="lg">SEO &amp; Analytics</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Search engine defaults, structured data, robots.txt, and Google Analytics.
                </p>
            
            <!-- TODO: x-card.footer -->
                <Button variant="primary" :href="route('admin.settings.seo')">
                    Configure
                </Button>
            
        

        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-bell -->
                    <Heading size="lg">Notifications</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Enable or disable notifications and choose delivery channels.
                </p>
            
            <!-- TODO: x-card.footer -->
                <Button variant="primary" :href="route('admin.settings.show', 'notifications')">
                    Configure
                </Button>
            
        
    </div>
</template>