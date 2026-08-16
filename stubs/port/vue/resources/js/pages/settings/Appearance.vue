<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Appearance" />

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
            <RadioGroupItem value="light" icon="sun">{{ __('Light') }}</RadioGroupItem>
            <RadioGroupItem value="dark" icon="moon">{{ __('Dark') }}</RadioGroupItem>
            <RadioGroupItem value="system" icon="computer-desktop">{{ __('System') }}</RadioGroupItem>
        </RadioGroup>
    
</section>
</template>