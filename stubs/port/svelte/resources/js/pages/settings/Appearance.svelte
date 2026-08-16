<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Appearance</title></svelte:head>

<AppLayout>

<!--
TODO (4):
 - inline Livewire PHP class moved to page <script> — port mount()/actions into setup state
 - @include: blade @include — extract to a shared component or inline
 - x-settings.layout blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<section class="w-full">


    <!-- TODO: x-settings.layout -->
        <RadioGroup x-data variant="segmented" x-model="$flux.appearance">
            <RadioGroupItem value="light" icon="sun">{__('Light')}</RadioGroupItem>
            <RadioGroupItem value="dark" icon="moon">{__('Dark')}</RadioGroupItem>
            <RadioGroupItem value="system" icon="computer-desktop">{__('System')}</RadioGroupItem>
        </RadioGroup>
    
</section>
</AppLayout>