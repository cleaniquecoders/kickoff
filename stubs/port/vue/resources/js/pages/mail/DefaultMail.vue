<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Default Mail" />

<!--
TODO (1):
 - x-mail blade component — port to kit equivalent or drop
-->

<!-- TODO: x-mail -->
Hi,

{{ $message }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
</template>