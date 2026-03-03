<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    message: { type: String, required: true },
});
</script>

<template>
    <Head title="Invitación" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Invitación
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <p class="mb-4 text-amber-800">
                            {{ message }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Podés volver al inicio desde la barra de navegación inferior.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
