<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Default Mail</title></svelte:head>

<AppLayout>

<!--
TODO (1):
 - x-mail blade component — port to kit equivalent or drop
-->

<!-- TODO: x-mail -->
Hi,

{$message}

Thanks,<br>
{config('app.name')}
</x-mail::message>
</AppLayout>