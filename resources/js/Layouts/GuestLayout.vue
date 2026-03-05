<script setup>
import UpdateRequiredModal from '@/Components/UpdateRequiredModal.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoUrl = page.props.logoUrl ?? '/images/logo.png';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div class="h-24 sm:h-28">
            <Link href="/" class="flex h-full items-center">
                <img
                    :src="logoUrl"
                    alt="Chanta Puntos"
                    class="block h-full w-auto object-contain object-left"
                />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>

        <!-- Contenido opcional fuera de la tarjeta (ej: link de registro) -->
        <slot name="below" />

        <UpdateRequiredModal />
    </div>
</template>
