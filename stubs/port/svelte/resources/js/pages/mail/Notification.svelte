<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Notification</title></svelte:head>

<AppLayout>

<!--
TODO (2):
 - x-mail blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!-- TODO: x-mail -->
{__('Hi')}!

{$message}

<!-- TODO: x-mail -->
	{(isset($url_text) && !empty($url_text)) ? $url_text : __('Click here')}
</x-mail::button>


Thanks,<br>
{config('app.name')}
</x-mail::message>
</AppLayout>