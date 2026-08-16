<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>User Form</title></svelte:head>

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
                {$editingUuid ? __('Edit User') : __('Add User')}
            </Heading>
            <Text class="mt-2">
                {{ $editingUuid
                    ? __('Update the account details below.')
                    : __('The new user will receive a link to set their own password.') }}
            </Text>
        </div>

        <form onsubmit={(e) => { e.preventDefault(); save; }} class="space-y-6">
            <Input bind:value={name} label={__('Name')} required autofocus />

            <Input bind:value={email} label={__('Email')} type="email" required />

                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {__('Changing the email resets the verification status.')}
                </p>


            <Fieldset>
                <Legend>{__('Roles')}</Legend>
                <div class="space-y-2">

                        <Checkbox bind:value={roles} value="{$role->name}"
                            label={$role->display_name} description={$role->description} />

                </div>

                    <Text class="mt-2 text-red-500">{$message}</Text>

            </Fieldset>

                <Checkbox bind:value={sendPasswordSetupLink}
                    label={__('Send password setup link')}
                    description={__('Email the user a link to set their password.')} />


            <div class="flex justify-end gap-3">
                <Button variant="ghost" onclick={close} class="cursor-pointer">
                    {__('Cancel')}
                </Button>
                <Button type="submit" variant="primary" class="cursor-pointer">
                    {$editingUuid ? __('Save Changes') : __('Create User')}
                </Button>
            </div>
        </form>
    </div>
</Dialog>
</AppLayout>