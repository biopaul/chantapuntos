<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    token: { type: String, required: true },
    inviterName: { type: String, required: true },
    childrenCount: { type: Number, required: true },
});

const form = useForm({});

function submit() {
    form.post(route('invitations.accept.process', props.token), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Aceptar invitación" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Aceptar invitación
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <p class="mb-4 text-gray-600">
                            <strong>{{ inviterName }}</strong> te ha invitado a ver y gestionar los mismos hijos en Chanta Puntos.
                        </p>
                        <p class="mb-6 text-gray-600">
                            Si aceptas, podrás sumar y restar puntos, canjear y ver el historial de los
                            {{ childrenCount }} {{ childrenCount === 1 ? 'hijo' : 'hijos' }}.
                        </p>
                        <form @submit.prevent="submit">
                            <PrimaryButton type="submit" :disabled="form.processing">
                                Aceptar invitación
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
