<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Authentication" />

<!--
TODO (6):
 - x-breadcrumbs blade component — port to kit equivalent or drop
 - x-card blade component — port to kit equivalent or drop
 - x-card.header blade component — port to kit equivalent or drop
 - x-lucide-user-plus blade component — port to kit equivalent or drop
 - x-card.body blade component — port to kit equivalent or drop
 - x-card.footer blade component — port to kit equivalent or drop
-->

<div>
    
    <!-- TODO: x-breadcrumbs -->

    <div class="flex items-end justify-between">
        <div>
            <Heading size="xl" level="1">Authentication</Heading>
            <Text class="mt-2">Control how users sign in and whether they can register themselves.</Text>
        </div>
    </div>

    <Separator variant="subtle" class="my-6" />

    <form @submit.prevent="save" class="max-w-2xl">
        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-user-plus -->
                    <Heading size="lg">Registration</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <Field variant="inline">
                    <Checkbox v-model="publicRegistrationEnabled" />
                    <Label>Allow public registration</Label>
                </Field>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    When enabled, anyone can create an account from the public sign-up page.
                    When disabled, the registration page is unavailable and only administrators
                    can add users.
                </p>
            
            <!-- TODO: x-card.footer -->
                <Button type="submit" variant="primary" icon="check" class="cursor-pointer">
                    Save changes
                </Button>
            
        
    </form>
</div>
</template>