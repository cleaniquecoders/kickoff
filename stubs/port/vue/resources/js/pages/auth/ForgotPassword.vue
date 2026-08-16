<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Forgot Password" />

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

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">


            <!-- Email Address -->
            <Input
                name="email"
                :label="__('Email Address')"
                type="email"
                required
                autofocus
                placeholder="email@example.com" />

            <Button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                {{ __('Email password reset link') }}
            </Button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('Or, return to') }}</span>
            <Link :href="route('login')">{{ __('log in') }}</Link>
        </div>
    </div>
</template>