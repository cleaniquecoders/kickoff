<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Recovery Codes" />

<!--
TODO (2):
 - inline Livewire PHP class moved to page <script> — port mount()/actions into setup state
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<div
    class="py-6 space-y-6 border shadow-sm rounded-xl border-zinc-200 dark:border-white/10"
    /* TODO: unmapped wire:cloak="" */
    x-data="{ showRecoveryCodes: false }"
>
    <div class="px-6 space-y-2">
        <div class="flex items-center gap-2">
            <LockClosed class="size-4" />
            <Heading size="lg" level="3">{{ __('2FA Recovery Codes') }}</Heading>
        </div>
        <Text variant="subtle">
            {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
        </Text>
    </div>

    <div class="px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <Button
                x-show="!showRecoveryCodes"
                icon="eye"
                icon:variant="outline"
                variant="primary"
                @click="showRecoveryCodes = true;"
                aria-expanded="false"
                aria-controls="recovery-codes-section"
            >
                {{ __('View Recovery Codes') }}
            </Button>

            <Button
                x-show="showRecoveryCodes"
                icon="eye-slash"
                icon:variant="outline"
                variant="primary"
                @click="showRecoveryCodes = false"
                aria-expanded="true"
                aria-controls="recovery-codes-section"
            >
                {{ __('Hide Recovery Codes') }}
            </Button>

                <Button
                    x-show="showRecoveryCodes"
                    icon="arrow-path"
                    variant="filled"
                    @click="regenerateRecoveryCodes"
                >
                    {{ __('Regenerate Codes') }}
                </Button>

        </div>

        <div
            x-show="showRecoveryCodes"
            x-transition
            id="recovery-codes-section"
            class="relative overflow-hidden"
            x-bind:aria-hidden="!showRecoveryCodes"
        >
            <div class="mt-3 space-y-3">

                    <Alert variant="danger" icon="x-circle" heading="{{$message}}" />

                    <div
                        class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-zinc-100 dark:bg-white/5"
                        role="list"
                        aria-label="Recovery codes"
                    >

                            <div
                                role="listitem"
                                class="select-text"
                                /* TODO: wire:loading.class — use form.processing / inertia progress */
                            >
                                {{ $code }}
                            </div>

                    </div>
                    <Text variant="subtle" class="text-xs">
                        {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate Codes above.') }}
                    </Text>

            </div>
        </div>
    </div>
</div>
</template>