<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Two Factor Challenge</title></svelte:head>

<AppLayout>

<!--
TODO (6):
 - @csrf: CSRF — Inertia handles via XSRF-TOKEN cookie automatically
 - x-layouts.auth blade component — port to kit equivalent or drop
 - x-auth-header blade component — port to kit equivalent or drop
 - x-input-otp blade component — port to kit equivalent or drop
 - Alpine $dispatch/$wire usage — port to Inertia router visit or emitted component event
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!-- TODO: x-layouts.auth -->
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $dispatch('clear-2fa-auth-code');
            
                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : $dispatch('focus-2fa-auth-code');
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <!-- TODO: x-auth-header -->
            </div>

            <div x-show="showRecoveryInput">
                <!-- TODO: x-auth-header -->
            </div>

            <form method="POST" action="{route('two-factor.login.store')}">


                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5">
                            <!-- TODO: x-input-otp -->
                        </div>

                            <Text color="red">
                                {$message}
                            </Text>

                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <Input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code" />
                        </div>

                            <Text color="red">
                                {$message}
                            </Text>

                    </div>

                    <Button
                        variant="primary"
                        type="submit"
                        class="w-full"
                    >
                        {__('Continue')}
                    </Button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                    <span class="opacity-50">{__('or you can')}</span>
                    <div class="inline font-medium underline cursor-pointer opacity-80">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{__('login using a recovery code')}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{__('login using an authentication code')}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</AppLayout>