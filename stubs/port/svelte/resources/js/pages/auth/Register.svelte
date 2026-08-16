<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Register</title></svelte:head>

<AppLayout>

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

        <form method="POST" action="{route('register.store')}" class="flex flex-col gap-6">

            <!-- Name -->
            <Input
                name="name"
                label={__('Name')}
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder={__('Full name')} />

            <!-- Email Address -->
            <Input
                name="email"
                label={__('Email address')}
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com" />

            <!-- Password -->
            <Input
                name="password"
                label={__('Password')}
                type="password"
                required
                autocomplete="new-password"
                placeholder={__('Password')}
                viewable />

            <!-- Confirm Password -->
            <Input
                name="password_confirmation"
                label={__('Confirm password')}
                type="password"
                required
                autocomplete="new-password"
                placeholder={__('Confirm password')}
                viewable />

            <div class="flex items-center justify-end">
                <Button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {__('Create account')}
                </Button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{__('Already have an account?')}</span>
            <Link href={route('login')}>{__('Log in')}</Link>
        </div>
    </div>
</AppLayout>