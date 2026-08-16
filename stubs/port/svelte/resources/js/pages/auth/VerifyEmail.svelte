<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Verify Email</title></svelte:head>

<AppLayout>

<!--
TODO (3):
 - @csrf: CSRF — Inertia handles via XSRF-TOKEN cookie automatically
 - x-layouts.auth blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!-- TODO: x-layouts.auth -->
    <div class="mt-4 flex flex-col gap-6">
        <Text class="text-center">
            {__('Please verify your email address by clicking on the link we just emailed to you.')}
        </Text>

            <Text class="text-center font-medium !dark:text-green-400 !text-green-600">
                {__('A new verification link has been sent to the email address you provided during registration.')}
            </Text>


        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{route('verification.send')}">

                <Button type="submit" variant="primary" class="w-full">
                    {__('Resend verification email')}
                </Button>
            </form>

            <form method="POST" action="{route('logout')}">

                <Button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {__('Log out')}
                </Button>
            </form>
        </div>
    </div>
</AppLayout>