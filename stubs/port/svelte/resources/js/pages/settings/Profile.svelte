<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Profile</title></svelte:head>

<AppLayout>

<!--
TODO (6):
 - inline Livewire PHP class moved to page <script> — port mount()/actions into setup state
 - @include: blade @include — extract to a shared component or inline
 - x-settings.layout blade component — port to kit equivalent or drop
 - x-action-message blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
 - livewire:settings.delete-user-form nested component — extract to child page component
-->

<section class="w-full">


    <!-- TODO: x-settings.layout -->
        <form onsubmit={(e) => { e.preventDefault(); updateProfileInformation; }} class="my-6 w-full space-y-6">
            <Input bind:value={name} label={__('Name')} type="text" required autofocus autocomplete="name" />

            <div>
                <Input bind:value={email} label={__('Email')} type="email" required autocomplete="email" />

                    <div>
                        <Text class="mt-4">
                            {__('Your email address is unverified.')}

                            <Link class="text-sm cursor-pointer" onclick={resendVerificationNotification}>
                                {__('Click here to re-send the verification email.')}
                            </Link>
                        </Text>

                            <Text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {__('A new verification link has been sent to your email address.')}
                            </Text>

                    </div>

            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <Button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {__('Save')}
                    </Button>
                </div>

                <!-- TODO: x-action-message -->
                    {__('Saved.')}
                
            </div>
        </form>

            <!-- TODO: livewire:settings.delete-user-form -->

    
</section>
</AppLayout>