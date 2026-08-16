<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Delete User Form</title></svelte:head>

<AppLayout>

<!--
TODO (3):
 - inline Livewire PHP class moved to page <script> — port mount()/actions into setup state
 - Alpine $dispatch/$wire usage — port to Inertia router visit or emitted component event
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <Heading>{__('Delete account')}</Heading>
        <Subheading>{__('Delete your account and all of its resources')}</Subheading>
    </div>

    <DialogTrigger name="confirm-user-deletion">
        <Button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" data-test="delete-user-button">
            {__('Delete account')}
        </Button>
    </DialogTrigger>

    <Dialog name="confirm-user-deletion" show={$errors->isNotEmpty()} focusable class="max-w-lg">
        <form method="POST" onsubmit={(e) => { e.preventDefault(); deleteUser; }} class="space-y-6">
            <div>
                <Heading size="lg">{__('Are you sure you want to delete your account?')}</Heading>

                <Subheading>
                    {__('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.')}
                </Subheading>
            </div>

            <Input bind:value={password} label={__('Password')} type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <DialogClose>
                    <Button variant="filled">{__('Cancel')}</Button>
                </DialogClose>

                <Button variant="danger" type="submit" data-test="confirm-delete-user-button">
                    {__('Delete account')}
                </Button>
            </div>
        </form>
    </Dialog>
</section>
</AppLayout>