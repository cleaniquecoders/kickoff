@props(['class' => 'h-8 w-8'])

{{-- Kickoff Logo Icon - Used in auth pages --}}
<x-kickoff-logo {{ $attributes->merge(['class' => $class]) }} />
