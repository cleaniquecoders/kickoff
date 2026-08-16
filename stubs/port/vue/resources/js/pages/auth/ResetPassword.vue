<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Reset Password" />

<!--
TODO (5):
 - @csrf: CSRF — Inertia handles via XSRF-TOKEN cookie automatically
 - x-layouts.auth blade component — port to kit equivalent or drop
 - x-auth-header blade component — port to kit equivalent or drop
 - x-auth-session-status blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!-- TODO: x-layouts.auth -->
    <div class="flex flex-col gap-6">
        <!-- TODO: x-auth-header -->

        <!-- Session Status -->
        <!-- TODO: x-auth-session-status -->

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">

            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <Input
                name="email"
                value="{{ request('email') }}"
                :label="__('Email')"
                type="email"
                required
                autocomplete="email" />

            <!-- Password -->
            <Input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable />

            <!-- Confirm Password -->
            <Input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable />

            <div class="flex items-center justify-end">
                <Button type="submit" variant="primary" class="w-full" data-test="reset-password-button">
                    {{ __('Reset password') }}
                </Button>
            </div>
        </form>
    </div>
</template>