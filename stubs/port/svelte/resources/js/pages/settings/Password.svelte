<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Password</title></svelte:head>

<AppLayout>

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
        <form method="POST" onsubmit={(e) => { e.preventDefault(); updatePassword; }} class="mt-6 space-y-6">
            <Input
                bind:value={current_password}
                label={__('Current password')}
                type="password"
                required
                autocomplete="current-password" />
            <Input
                bind:value={password}
                label={__('New password')}
                type="password"
                required
                autocomplete="new-password" />
            <Input
                bind:value={password_confirmation}
                label={__('Confirm Password')}
                type="password"
                required
                autocomplete="new-password" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <Button variant="primary" type="submit" class="w-full" data-test="update-password-button">
                        {__('Save')}
                    </Button>
                </div>

                <!-- TODO: x-action-message -->
                    {__('Saved.')}
                
            </div>
        </form>
    
</section>
</AppLayout>