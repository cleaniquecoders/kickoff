<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="G8desk" />

<!--
TODO (6):
 - x-breadcrumbs blade component — port to kit equivalent or drop
 - x-card blade component — port to kit equivalent or drop
 - x-card.header blade component — port to kit equivalent or drop
 - x-lucide-life-buoy blade component — port to kit equivalent or drop
 - x-card.body blade component — port to kit equivalent or drop
 - x-card.footer blade component — port to kit equivalent or drop
-->

<div>
    
    <!-- TODO: x-breadcrumbs -->

    <div class="flex items-end justify-between">
        <div>
            <Heading size="xl" level="1">Support</Heading>
            <Text class="mt-2">Embed the g8desk support widget so signed-in users can open tickets without leaving the app.</Text>
        </div>
    </div>

    <Separator variant="subtle" class="my-6" />

    <form @submit.prevent="save" class="max-w-2xl">
        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-life-buoy -->
                    <Heading size="lg">Support Widget</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <Field variant="inline">
                    <Switch v-model="enabled" />
                    <Label>Enable the g8desk support widget</Label>
                </Field>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    No SDK, no npm package — when enabled, the widget loads directly from g8desk
                    for authenticated users on every app page (~5 lines of markup).
                </p>

                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-xs text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-400">
                    Need keys? In g8desk, open
                    <a href="{{ rtrim($baseUrl ?: 'https://g8desk.com', '/') }}/ops/organizations" target="_blank" rel="noopener noreferrer" class="font-medium text-brand-500 hover:underline">Ops → your organisation → Intake Channels</a>,
                    create a channel, then <strong>Widget → Generate embed</strong> to reveal the public key and secret to paste below.
                </div>

                <div>
                    <Input v-model="baseUrl" type="url" :label="__('Base URL')" placeholder="https://g8desk.com" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The g8desk base URL. The widget script loads from <code>{base_url}/intake/widget.js</code>.
                    </p>
                </div>

                <div>
                    <Input v-model="publicKey" :label="__('Public Key')" placeholder="pk_..." />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The pk_… data-key from g8desk → channel → Widget → Generate embed.
                    </p>
                </div>

                <div>
                    <Input v-model="widgetSecret" type="password" :label="__('Widget Secret')" placeholder="g8wi_..." />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The g8wi_… identity secret; stored encrypted, never sent to the browser.
                    </p>
                </div>
            
            <!-- TODO: x-card.footer -->
                <Button type="submit" variant="primary" icon="check" class="cursor-pointer">
                    Save changes
                </Button>
            
        
    </form>
</div>
</template>