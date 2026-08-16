<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Mcp Tokens" />

<!--
TODO (3):
 - x-breadcrumbs blade component — port to kit equivalent or drop
 - x-card blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<div>
    
    <!-- TODO: x-breadcrumbs -->

    <div class="flex items-end justify-between">
        <div>
            <Heading size="xl" level="1">{{ __('MCP Tokens') }}</Heading>
            <Text class="mt-2">{{ __('Connect AI clients (Claude Code, Claude Desktop) to the MCP server.') }}</Text>
        </div>
    </div>

    <Separator variant="subtle" class="my-6" />

    <div class="space-y-6">

            <Alert variant="success" icon="key">
                <AlertTitle>{{ __('Token created — copy it now') }}</AlertTitle>
                <AlertDescription>
                    {{ __('This is the only time the token is shown.') }}
                    <Input readonly copyable value="{{ $plainTextToken }}" class="mt-2" />
                    <p class="mt-3 text-sm">{{ __('Add it to Claude:') }}</p>
                    <Input readonly copyable class="mt-1"
                        value="claude mcp add --transport http mcp-kit {{ route('mcp-kit.tasks') }} --header &quot;Authorization: Bearer {{ $plainTextToken }}&quot;" />
                </AlertDescription>
            </Alert>


        
        <!-- TODO: x-card -->
            <div class="p-6">
                <Heading size="lg">{{ __('Token name') }}</Heading>
                <Text class="mt-1">{{ __('A label to recognise where this token is used.') }}</Text>

                <form @submit.prevent="createToken" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div class="flex-1">
                        <Input
                            v-model="tokenName"
                            placeholder="{{ __('e.g. claude-code-laptop') }}" />
                    </div>
                    <Button type="submit" variant="primary" icon="key" class="cursor-pointer">
                        {{ __('Generate Token') }}
                    </Button>
                </form>
            </div>
        

        
        <!-- TODO: x-card -->
            <div class="p-6">
                <Heading size="lg">{{ __('Personal Access Tokens') }}</Heading>
                <Text class="mt-1">{{ __('Tokens you have generated for header-capable AI clients.') }}</Text>

                <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">

                        <div :key="token-{{ $token->id }}" /* TODO: wire:key — use a real loop key */ class="flex items-center justify-between gap-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $token->name }}</p>
                                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ __('Created :when', ['when' => $token->created_at->diffForHumans()]) }}
                                    · {{ __('Last used :when', ['when' => $token->last_used_at?->diffForHumans() ?? __('never')]) }}
                                </p>
                            </div>
                            <Button
                                size="sm"
                                variant="danger"
                                icon="trash-2"
                                class="cursor-pointer"
                                @click="revoke('{{ $token->id }}')"
                                data-confirm="{{ __(&#039;Revoke this token? Clients using it will lose access.&#039;) }}" /* TODO: wire confirm() into click handler */
                            >
                                {{ __('Revoke') }}
                            </Button>
                        </div>

                        <div class="flex flex-col items-center justify-center gap-2 py-10 text-center">
                            <Key class="size-4" />
                            <Text class="text-zinc-500 dark:text-zinc-400">
                                {{ __('No tokens yet. Generate one above to connect an AI client.') }}
                            </Text>
                        </div>

                </div>
            </div>
        

        
        <!-- TODO: x-card -->
            <div class="p-6">
                <Heading size="lg">{{ __('Connected Apps') }}</Heading>
                <Text class="mt-1">
                    {{ __('AI clients connected via OAuth (e.g. Claude on claude.ai). They authorize through your login — no token paste needed.') }}
                </Text>

                <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">

                        <div :key="app-{{ $app->id }}" /* TODO: wire:key — use a real loop key */ class="flex items-center justify-between gap-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $app->client->name ?? __('Unknown') }}</p>
                                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ __('Connected :when', ['when' => $app->created_at->diffForHumans()]) }}
                                </p>
                            </div>
                        </div>

                        <Text class="block py-4 text-zinc-500 dark:text-zinc-400">
                            {{ __('No connected apps. In Claude, add a custom connector pointing to the MCP endpoint and authorize it.') }}
                        </Text>

                </div>
            </div>
        
    </div>
</div>
</template>