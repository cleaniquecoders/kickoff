<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Two Factor" />

<!--
TODO (7):
 - inline Livewire PHP class moved to page <script> — port mount()/actions into setup state
 - @include: blade @include — extract to a shared component or inline
 - x-settings.layout blade component — port to kit equivalent or drop
 - x-input-otp blade component — port to kit equivalent or drop
 - Alpine $dispatch/$wire usage — port to Inertia router visit or emitted component event
 - verify blade echos against server-provided Inertia props (routes, translations)
 - livewire:settings.two-factor.recovery-codes nested component — extract to child page component
-->

<section class="w-full">


    <!-- TODO: x-settings.layout -->
        <div class="flex flex-col w-full mx-auto space-y-6 text-sm" /* TODO: unmapped wire:cloak="" */>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <Badge color="green">{{ __('Enabled') }}</Badge>
                    </div>

                    <Text>
                        {{ __('With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                    </Text>

                    <!-- TODO: livewire:settings.two-factor.recovery-codes -->

                    <div class="flex justify-start">
                        <Button
                            variant="danger"
                            icon="shield-exclamation"
                            icon:variant="outline"
                            @click="disable"
                        >
                            {{ __('Disable 2FA') }}
                        </Button>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <Badge color="red">{{ __('Disabled') }}</Badge>
                    </div>

                    <Text variant="subtle">
                        {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                    </Text>

                    <Button
                        variant="primary"
                        icon="shield-check"
                        icon:variant="outline"
                        @click="enable"
                    >
                        {{ __('Enable 2FA') }}
                    </Button>
                </div>

        </div>
    

    <Dialog
        name="two-factor-setup-modal"
        class="max-w-md md:min-w-md"
        @close="closeModal"
        v-model="showModal"
    >
        <div class="space-y-6">
            <div class="flex flex-col items-center space-y-4">
                <div class="p-0.5 w-auto rounded-full border border-stone-100 dark:border-stone-600 bg-white dark:bg-stone-800 shadow-sm">
                    <div class="p-2.5 rounded-full border border-stone-200 dark:border-stone-600 overflow-hidden bg-stone-100 dark:bg-stone-200 relative">
                        <div class="flex items-stretch absolute inset-0 w-full h-full divide-x [&>div]:flex-1 divide-stone-200 dark:divide-stone-300 justify-around opacity-50">

                                <div></div>

                        </div>

                        <div class="flex flex-col items-stretch absolute w-full h-full divide-y [&>div]:flex-1 inset-0 divide-stone-200 dark:divide-stone-300 justify-around opacity-50">

                                <div></div>

                        </div>

                        <QrCode class="size-4" />
                    </div>
                </div>

                <div class="space-y-2 text-center">
                    <Heading size="lg">{{ $this->modalConfig['title'] }}</Heading>
                    <Text>{{ $this->modalConfig['description'] }}</Text>
                </div>
            </div>

                <div class="space-y-6">
                    <div class="flex flex-col items-center space-y-3">
                        <!-- TODO: x-input-otp -->

                            <Text color="red">
                                {{ $message }}
                            </Text>

                    </div>

                    <div class="flex items-center space-x-3">
                        <Button
                            variant="outline"
                            class="flex-1"
                            @click="resetVerification"
                        >
                            {{ __('Back') }}
                        </Button>

                        <Button
                            variant="primary"
                            class="flex-1"
                            @click="confirmTwoFactor"
                            x-bind:disabled="$wire.code.length < 6"
                        >
                            {{ __('Confirm') }}
                        </Button>
                    </div>
                </div>

                    <Alert variant="danger" icon="x-circle" heading="{{ $message }}" />


                <div class="flex justify-center">
                    <div class="relative w-64 overflow-hidden border rounded-lg border-stone-200 dark:border-stone-700 aspect-square">

                            <div class="absolute inset-0 flex items-center justify-center bg-white dark:bg-stone-700 animate-pulse">
                                <Loading class="size-4" />
                            </div>

                            <div class="flex items-center justify-center h-full p-4">
                                <div class="bg-white p-3 rounded">
                                    {!! $qrCodeSvg !!}
                                </div>
                            </div>

                    </div>
                </div>

                <div>
                    <Button
                        :disabled="$errors->has('setupData')"
                        variant="primary"
                        class="w-full"
                        @click="showVerificationIfNecessary"
                    >
                        {{ $this->modalConfig['buttonText'] }}
                    </Button>
                </div>

                <div class="space-y-4">
                    <div class="relative flex items-center justify-center w-full">
                        <div class="absolute inset-0 w-full h-px top-1/2 bg-stone-200 dark:bg-stone-600"></div>
                        <span class="relative px-2 text-sm bg-white dark:bg-stone-800 text-stone-600 dark:text-stone-400">
                            {{ __('or, enter the code manually') }}
                        </span>
                    </div>

                    <div
                        class="flex items-center space-x-2"
                        x-data="{
                            copied: false,
                            async copy() {
                                try {
                                    await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 1500);
                                } catch (e) {
                                    console.warn('Could not copy to clipboard');
                                }
                            }
                        }"
                    >
                        <div class="flex items-stretch w-full border rounded-xl dark:border-stone-700">

                                <div class="flex items-center justify-center w-full p-3 bg-stone-100 dark:bg-stone-700">
                                    <Loading class="size-4" />
                                </div>

                                <input
                                    type="text"
                                    readonly
                                    value="{{ $manualSetupKey }}"
                                    class="w-full p-3 bg-transparent outline-none text-stone-900 dark:text-stone-100"
                                />

                                <button
                                    @click="copy()"
                                    class="px-3 transition-colors border-l cursor-pointer border-stone-200 dark:border-stone-600"
                                >
                                    <DocumentDuplicate class="size-4" /></LucideIcon>
                                    <Check class="size-4" /></LucideIcon>
                                </button>

                        </div>
                    </div>
                </div>

        </div>
    </Dialog>
</section>
</template>