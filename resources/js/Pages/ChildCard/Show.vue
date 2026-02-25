<script setup>
import ChildIcon from '@/Components/ChildIcon.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    child: { type: Object, required: true },
    transactions: { type: Array, default: () => [] },
});

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('es', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function description(t) {
    if (t.type === 'redeem') return t.description;
    return t.action?.name ?? '—';
}
</script>

<template>
    <Head :title="'Ficha de ' + child.name" />

    <div class="min-h-screen bg-gray-100 py-8">
        <div class="mx-auto max-w-lg px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white shadow-md">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-6 text-center">
                    <div class="flex justify-center">
                        <ChildIcon
                            :icon="child.icon"
                            :avatar-url="child.avatar_url"
                            size="lg"
                        />
                    </div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ child.name }}</h1>
                    <p
                        class="mt-2 text-3xl font-bold"
                        :class="child.points >= 0 ? 'text-indigo-600' : 'text-red-600'"
                    >
                        {{ child.points }} pts
                    </p>
                </div>
                <div class="p-6">
                    <h2 class="mb-3 text-sm font-medium uppercase text-gray-500">
                        Historial de puntos
                    </h2>
                    <div class="space-y-2">
                        <div
                            v-for="t in transactions"
                            :key="t.id"
                            class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 text-sm"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900">{{ description(t) }}</p>
                                <p class="text-xs text-gray-500">{{ formatDate(t.created_at) }}</p>
                            </div>
                            <span
                                class="ml-2 shrink-0 font-semibold"
                                :class="t.points > 0 ? 'text-green-600' : 'text-red-600'"
                            >
                                {{ t.points > 0 ? '+' : '' }}{{ t.points }}
                            </span>
                        </div>
                    </div>
                    <p v-if="transactions.length === 0" class="py-6 text-center text-gray-500">
                        Aún no hay movimientos.
                    </p>
                </div>
            </div>
            <p class="mt-4 text-center text-xs text-gray-400">
                Solo lectura · Chanta Puntos
            </p>
        </div>
    </div>
</template>
