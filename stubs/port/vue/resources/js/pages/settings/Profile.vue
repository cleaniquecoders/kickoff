<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Profile" />

<!--
TODO (6):
 - inline Livewire PHP class moved to page <script> — port mount()/actions into setup state
 - @include: blade @include — extract to a shared component or inline
 - x-settings.layout blade component — port to kit equivalent or drop
 - x-action-message blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
 - livewire:settings.delete-user-form nested component — extract to child page component
-->

<section class="w-full">


    <!-- TODO: x-settings.layout -->
        <form @submit.prevent="updateProfileInformation" class="my-6 w-full space-y-6">
            <Input v-model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <Input v-model="email" :label="__('Email')" type="email" required autocomplete="email" />

                    <div>
                        <Text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <Link class="text-sm cursor-pointer" @click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </Link>
                        </Text>

                            <Text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </Text>

                    </div>

            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <Button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </Button>
                </div>

                <!-- TODO: x-action-message -->
                    {{ __('Saved.') }}
                
            </div>
        </form>

            <!-- TODO: livewire:settings.delete-user-form -->

    
</section>
</template>