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
TODO (9):
 - @can: authorization check — move to controller-provided `can` prop
 - x-layouts.app blade component — port to kit equivalent or drop
 - x-breadcrumbs blade component — port to kit equivalent or drop
 - x-card blade component — port to kit equivalent or drop
 - x-card.header blade component — port to kit equivalent or drop
 - x-lucide-shield-check blade component — port to kit equivalent or drop
 - x-card.body blade component — port to kit equivalent or drop
 - x-card.footer blade component — port to kit equivalent or drop
 - x-lucide-settings blade component — port to kit equivalent or drop
-->

<!-- TODO: x-layouts.app -->
    <div class="mx-auto max-w-4xl">
        <!-- TODO: x-breadcrumbs -->

        <Heading size="xl" level="1" class="mb-6">Administration</Heading>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">

                <!-- TODO: x-card -->
                    <!-- TODO: x-card.header -->
                        <div class="flex items-center">
                            <!-- TODO: x-lucide-shield-check -->
                            <Heading size="lg">Roles</Heading>
                        </div>
                    
                    <!-- TODO: x-card.body -->
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            Manage user roles and permissions
                        </p>
                    
                    <!-- TODO: x-card.footer -->
                        <Button variant="primary" :href="route('admin.roles.index')">
                            Manage Roles
                        </Button>
                    
                


            <!-- TODO: x-card -->
                <!-- TODO: x-card.header -->
                    <div class="flex items-center">
                        <!-- TODO: x-lucide-settings -->
                        <Heading size="lg">Settings</Heading>
                    </div>
                
                <!-- TODO: x-card.body -->
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Configure application settings
                    </p>
                
                <!-- TODO: x-card.footer -->
                    <Button variant="primary" :href="route('admin.settings.index')">
                        Manage Settings
                    </Button>
                
            
        </div>
    </div>
</template>