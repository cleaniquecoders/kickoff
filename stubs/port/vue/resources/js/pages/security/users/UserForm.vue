<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="User Form" />

<!--
TODO (2):
 - @unless: conditional — port to template if/else with server-provided prop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<Dialog variant="flyout" v-model="showing" class="max-w-md">
    <div class="space-y-6">
        <div>
            <Heading size="lg">
                {{ $editingUuid ? __('Edit User') : __('Add User') }}
            </Heading>
            <Text class="mt-2">
                {{ $editingUuid
                    ? __('Update the account details below.')
                    : __('The new user will receive a link to set their own password.') }}
            </Text>
        </div>

        <form @submit.prevent="save" class="space-y-6">
            <Input v-model="name" :label="__('Name')" required autofocus />

            <Input v-model="email" :label="__('Email')" type="email" required />

                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Changing the email resets the verification status.') }}
                </p>


            <Fieldset>
                <Legend>{{ __('Roles') }}</Legend>
                <div class="space-y-2">

                        <Checkbox v-model="roles" value="{{ $role->name }}"
                            :label="$role->display_name" :description="$role->description" />

                </div>

                    <Text class="mt-2 text-red-500">{{ $message }}</Text>

            </Fieldset>

                <Checkbox v-model="sendPasswordSetupLink"
                    :label="__('Send password setup link')"
                    :description="__('Email the user a link to set their password.')" />


            <div class="flex justify-end gap-3">
                <Button variant="ghost" @click="close" class="cursor-pointer">
                    {{ __('Cancel') }}
                </Button>
                <Button type="submit" variant="primary" class="cursor-pointer">
                    {{ $editingUuid ? __('Save Changes') : __('Create User') }}
                </Button>
            </div>
        </form>
    </div>
</Dialog>
</template>