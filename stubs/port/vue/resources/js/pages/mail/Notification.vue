<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Notification" />

<!--
TODO (2):
 - x-mail blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!-- TODO: x-mail -->
{{ __('Hi') }}!

{{ $message }}

<!-- TODO: x-mail -->
	{{ (isset($url_text) && !empty($url_text)) ? $url_text : __('Click here') }}
</x-mail::button>


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
</template>