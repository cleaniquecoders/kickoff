<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Role Form" />

<!--
TODO (2):
 - @unless: conditional — port to template if/else with server-provided prop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<Dialog variant="flyout" v-model="showing" class="max-w-md">
    <div class="space-y-6">
        <div>
            <Heading size="lg">
                {{ $editingUuid ? __('Edit Role') : __('Add Role') }}
            </Heading>
            <Text class="mt-2">
                {{ $editingUuid
                    ? __('Update the role details below. The internal name cannot be changed.')
                    : __('The internal name is derived from the display name.') }}
            </Text>
        </div>

        <form @submit.prevent="save" class="space-y-6">
            <Input v-model="displayName" :label="__('Display Name')" required autofocus />

            <Textarea v-model="description" :label="__('Description')" rows="3" />

                <Switch v-model="isEnabled" :label="__('Enabled')" />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Disabled roles cannot be assigned to users.') }}
                </p>


            <div class="flex justify-end gap-3">
                <Button variant="ghost" @click="close" class="cursor-pointer">
                    {{ __('Cancel') }}
                </Button>
                <Button type="submit" variant="primary" class="cursor-pointer">
                    {{ $editingUuid ? __('Save Changes') : __('Create Role') }}
                </Button>
            </div>
        </form>
    </div>
</Dialog>
</template>