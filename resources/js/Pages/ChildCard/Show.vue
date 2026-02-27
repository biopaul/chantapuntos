<script setup>
import ChildIcon from '@/Components/ChildIcon.vue';
import Skeleton from '@/Components/Skeleton.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    child: { type: Object, required: true },
    transactions: { type: Array, default: () => [] },
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
                    <template v-if="!showContent">
                        <div class="flex justify-center">
                            <Skeleton variant="circle" class="h-20 w-20" />
                        </div>
                        <Skeleton variant="line" class="mx-auto mt-3 h-6 w-32" />
                        <Skeleton variant="line" class="mx-auto mt-2 h-8 w-20" />
                        <Skeleton variant="line" class="mx-auto mt-1 h-4 w-28" />
                    </template>
                    <template v-else>
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
                    <p class="mt-1 text-sm text-gray-500">
                        Total pts ganados: {{ child.total_points_earned ?? 0 }}
                    </p>
                    </template>
                </div>
                <div class="p-6">
                    <template v-if="!showContent">
                        <Skeleton variant="line" class="mb-3 h-4 w-36" />
                        <div class="space-y-2">
                            <div
                                v-for="n in 4"
                                :key="'sk-' + n"
                                class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-4 py-3"
                            >
                                <div class="flex-1 space-y-1">
                                    <Skeleton variant="line" class="h-4 w-40" />
                                    <Skeleton variant="line" class="h-3 w-24" />
                                </div>
                                <Skeleton variant="line" class="h-4 w-12" />
                            </div>
                        </div>
                    </template>
                    <template v-else>
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
                    </template>
                </div>
            </div>
            <p class="mt-4 text-center text-xs text-gray-400">
                Solo lectura · Chanta Puntos
            </p>
        </div>
    </div>
</template>
