<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Login</title></svelte:head>

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

        <form method="POST" action="{route('login.store')}" class="flex flex-col gap-6">


            <!-- Email Address -->
            <Input
                name="email"
                label={__('Email address')}
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com" />

            <!-- Password -->
            <div class="relative">
                <Input
                    name="password"
                    label={__('Password')}
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder={__('Password')}
                    viewable />

                    <Link class="absolute top-0 text-sm end-0" href={route('password.request')}>
                        {__('Forgot your password?')}
                    </Link>

            </div>

            <!-- Remember Me -->
            <Checkbox name="remember" label={__('Remember me')} checked={old('remember')} />

            <div class="flex items-center justify-end">
                <Button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {__('Log in')}
                </Button>
            </div>
        </form>

            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{__('Don\'t have an account?')}</span>
                <Link href={route('register')}>{__('Sign up')}</Link>
            </div>

    </div>
</AppLayout>