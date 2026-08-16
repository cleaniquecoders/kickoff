<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Show" />

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
            <Subheading>{{ $sub ?? 'View audit log details' }}</Subheading>
        </div>
        <Button variant="ghost" icon="arrow-left" href="{{ route('security.audit-trail.index') }}" class="cursor-pointer">
            Back
        </Button>
    </div>
</template>