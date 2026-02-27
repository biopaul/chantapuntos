<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Skeleton from '@/Components/Skeleton.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    transactions: { type: Array, required: true },
});

const showContent = ref(false);
onMounted(() => {
    requestAnimationFrame(() => {
        setTimeout(() => {
            showContent.value = true;
        }, 220);
    });
});

function formatDate(dateStr) {
    const d = new Date(dateStr);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = String(d.getFullYear()).slice(-2);
    return `${day}/${month}/${year}`;
}

function description(t) {
    if (t.type === 'redeem') return t.description;
    return t.action?.name ?? '—';
}
</script>

<template>
    <Head title="Historial" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Historial de puntos
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <p class="mb-4 text-gray-600">
                            Últimos movimientos de puntos por hijo.
                        </p>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                            Fecha
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                            Hijo
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                            Concepto
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                            Puntos
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template v-if="!showContent">
                                        <tr v-for="n in 8" :key="'sk-' + n" class="divide-y divide-gray-200">
                                            <td class="px-4 py-3"><Skeleton variant="line" class="w-20" /></td>
                                            <td class="px-4 py-3"><Skeleton variant="line" class="w-24" /></td>
                                            <td class="px-4 py-3"><Skeleton variant="line" class="w-32" /></td>
                                            <td class="px-4 py-3 text-right"><Skeleton variant="line" class="ml-auto w-12" /></td>
                                        </tr>
                                    </template>
                                    <tr v-else v-for="t in transactions" :key="t.id">
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                            {{ formatDate(t.created_at) }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                            {{ t.child?.name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ description(t) }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium">
                                            <span
                                                :class="
                                                    t.points > 0
                                                        ? 'text-green-600'
                                                        : 'text-red-600'
                                                "
                                            >
                                                {{ t.points > 0 ? '+' : '' }}{{ t.points }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p v-if="transactions.length === 0" class="py-8 text-center text-gray-500">
                            Aún no hay movimientos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
