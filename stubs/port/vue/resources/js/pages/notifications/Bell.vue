<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Bell" />

<!--
TODO (2):
 - x-icon blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<div>
    <DropdownMenu position="bottom" align="end">
        <Button variant="ghost" icon="bell" class="relative">

                <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-medium text-white">
                    {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
                </span>

        </Button>

        <DropdownMenuContent class="w-80">
            <DropdownMenuLabel class="flex items-center justify-between">
                <span>{{ __('Notifications') }}</span>

                    <Badge color="amber" size="sm">
                        {{ $this->unreadCount }} {{ __('unread') }}
                    </Badge>

            </DropdownMenuLabel>

            <DropdownMenuSeparator />

                <DropdownMenuItem
                    @click="markAsRead('{{ $notification->id }}')"
                    class="flex flex-col items-start gap-1 !py-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        <span class="font-medium text-zinc-900 dark:text-white">
                            {{ data_get($notification->data, 'subject', class_basename($notification->type)) }}
                        </span>
                    </div>

                        <span class="ml-4 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">
                            {{ $message }}
                        </span>

                    <span class="ml-4 text-xs text-zinc-400 dark:text-zinc-500">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </DropdownMenuItem>

                <div class="py-6 text-center">
                    <!-- TODO: x-icon -->
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No unread notifications') }}
                    </p>
                </div>


            <DropdownMenuSeparator />

            <DropdownMenuItem :href="route('notifications.index')" icon="chevron-right">
                {{ __('View All Notifications') }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</div>
</template>