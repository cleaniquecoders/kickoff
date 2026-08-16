<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Role Form</title></svelte:head>

<AppLayout>

<!--
TODO (2):
 - @unless: conditional — port to template if/else with server-provided prop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<Dialog variant="flyout" bind:value={showing} class="max-w-md">
    <div class="space-y-6">
        <div>
            <Heading size="lg">
                {$editingUuid ? __('Edit Role') : __('Add Role')}
            </Heading>
            <Text class="mt-2">
                {{ $editingUuid
                    ? __('Update the role details below. The internal name cannot be changed.')
                    : __('The internal name is derived from the display name.') }}
            </Text>
        </div>

        <form onsubmit={(e) => { e.preventDefault(); save; }} class="space-y-6">
            <Input bind:value={displayName} label={__('Display Name')} required autofocus />

            <Textarea bind:value={description} label={__('Description')} rows="3" />

                <Switch bind:value={isEnabled} label={__('Enabled')} />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {__('Disabled roles cannot be assigned to users.')}
                </p>


            <div class="flex justify-end gap-3">
                <Button variant="ghost" onclick={close} class="cursor-pointer">
                    {__('Cancel')}
                </Button>
                <Button type="submit" variant="primary" class="cursor-pointer">
                    {$editingUuid ? __('Save Changes') : __('Create Role')}
                </Button>
            </div>
        </form>
    </div>
</Dialog>
</AppLayout>