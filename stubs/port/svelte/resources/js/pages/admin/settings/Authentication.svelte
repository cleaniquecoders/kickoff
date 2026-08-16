<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Authentication</title></svelte:head>

<AppLayout>

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

    <form onsubmit={(e) => { e.preventDefault(); save; }} class="max-w-2xl">
        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-user-plus -->
                    <Heading size="lg">Registration</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <Field variant="inline">
                    <Checkbox bind:value={publicRegistrationEnabled} />
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
</AppLayout>