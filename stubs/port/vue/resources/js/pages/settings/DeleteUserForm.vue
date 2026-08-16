<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Delete User Form" />

<!--
TODO (3):
 - inline Livewire PHP class moved to page <script> — port mount()/actions into setup state
 - Alpine $dispatch/$wire usage — port to Inertia router visit or emitted component event
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <Heading>{{ __('Delete account') }}</Heading>
        <Subheading>{{ __('Delete your account and all of its resources') }}</Subheading>
    </div>

    <DialogTrigger name="confirm-user-deletion">
        <Button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" data-test="delete-user-button">
            {{ __('Delete account') }}
        </Button>
    </DialogTrigger>

    <Dialog name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form method="POST" @submit.prevent="deleteUser" class="space-y-6">
            <div>
                <Heading size="lg">{{ __('Are you sure you want to delete your account?') }}</Heading>

                <Subheading>
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </Subheading>
            </div>

            <Input v-model="password" :label="__('Password')" type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <DialogClose>
                    <Button variant="filled">{{ __('Cancel') }}</Button>
                </DialogClose>

                <Button variant="danger" type="submit" data-test="confirm-delete-user-button">
                    {{ __('Delete account') }}
                </Button>
            </div>
        </form>
    </Dialog>
</section>
</template>