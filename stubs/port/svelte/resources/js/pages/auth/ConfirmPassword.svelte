<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Confirm Password</title></svelte:head>

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

        <!-- TODO: x-auth-session-status -->

        <form method="POST" action="{route('password.confirm.store')}" class="flex flex-col gap-6">


            <Input
                name="password"
                label={__('Password')}
                type="password"
                required
                autocomplete="current-password"
                placeholder={__('Password')}
                viewable />

            <Button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                {__('Confirm')}
            </Button>
        </form>
    </div>
</AppLayout>