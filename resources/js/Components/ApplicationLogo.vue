<script setup>
import { usePage } from '@inertiajs/vue3';

const logoUrl = usePage().props.logoUrl ?? '/images/logo.png';
</script>

<template>
    <img
        :src="logoUrl"
        alt="Chanta Puntos"
        class="block w-auto min-w-0 object-contain object-left"
    />
</template>
