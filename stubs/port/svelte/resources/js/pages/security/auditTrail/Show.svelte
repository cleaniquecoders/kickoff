<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Show</title></svelte:head>

<AppLayout>

<!--
TODO (4):
 - @include: blade @include — extract to a shared component or inline
 - x-layouts.app blade component — port to kit equivalent or drop
 - x-breadcrumbs blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!-- TODO: x-layouts.app -->
    <!-- TODO: x-breadcrumbs -->

    <div class="mb-6 flex items-center justify-between">
        <div>
            <Heading size="xl" level="1">Audit Details</Heading>
            <Subheading>{$sub ?? 'View audit log details'}</Subheading>
        </div>
        <Button variant="ghost" icon="arrow-left" href="{route('security.audit-trail.index')}" class="cursor-pointer">
            Back
        </Button>
    </div>
</AppLayout>