<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Password" />

<!--
TODO (5):
 - inline Livewire PHP class moved to page <script> — port mount()/actions into setup state
 - @include: blade @include — extract to a shared component or inline
 - x-settings.layout blade component — port to kit equivalent or drop
 - x-action-message blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<section class="w-full">


    <!-- TODO: x-settings.layout -->
        <form method="POST" @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <Input
                v-model="current_password"
                :label="__('Current password')"
                type="password"
                required
                autocomplete="current-password" />
            <Input
                v-model="password"
                :label="__('New password')"
                type="password"
                required
                autocomplete="new-password" />
            <Input
                v-model="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <Button variant="primary" type="submit" class="w-full" data-test="update-password-button">
                        {{ __('Save') }}
                    </Button>
                </div>

                <!-- TODO: x-action-message -->
                    {{ __('Saved.') }}
                
            </div>
        </form>
    
</section>
</template>