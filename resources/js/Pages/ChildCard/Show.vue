<script setup>
import ChildIcon from '@/Components/ChildIcon.vue';
import Skeleton from '@/Components/Skeleton.vue';
import {
    ACHIEVEMENTS,
    getCurrentAchievement,
    getNextAchievement,
    getPointsToNext,
    getDisplayName,
    STAGE_COLORS,
} from '@/utils/achievements';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

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

const totalPts = computed(() => props.child.total_points_earned ?? 0);
const currentAchievement = computed(() => getCurrentAchievement(totalPts.value));
const nextAchievement = computed(() => getNextAchievement(totalPts.value));
const pointsToNext = computed(() => getPointsToNext(totalPts.value));

// ACHIEVEMENTS ya está en orden ascendente (10 → 500), se usa directamente.
// Los logros alcanzados aparecen arriba; al hacer scroll se descubren los futuros.
const achievementsAsc = ACHIEVEMENTS;

// Posición vertical del avatar en la barra lateral (% desde arriba),
// alineado con la fila del logro actual en la lista.
const avatarTopPercent = computed(() => {
    if (!currentAchievement.value) return 100;
    const idx = achievementsAsc.findIndex(a => a.points === currentAchievement.value.points);
    return ((idx + 0.5) / achievementsAsc.length) * 100;
});

// Altura de la barra de progreso rellena (% desde arriba hasta el logro actual).
const progressFillPercent = computed(() => {
    if (!currentAchievement.value) return 0;
    const idx = achievementsAsc.findIndex(a => a.points === currentAchievement.value.points);
    return ((idx + 1) / achievementsAsc.length) * 100;
});

// URL base de assets (inyectada por Laravel en app.blade.php)
const assetBase = document.head
    .querySelector('meta[name="asset-url"]')
    ?.getAttribute('content')
    ?.replace(/\/$/, '') ?? '';

function iconUrl(level) {
    return `${assetBase}/images/achievements/${level}.png`;
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
            <!-- Sección Mi Evolución -->
            <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-md">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-800">Mi Evolución</h2>
                </div>

                <template v-if="!showContent">
                    <div class="flex justify-center p-8">
                        <div class="h-64 w-8 rounded-full bg-gray-200" />
                    </div>
                </template>

                <template v-else>
                    <div class="p-6">
                        <!-- Resumen de estado -->
                        <div class="mb-6 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-lg font-bold text-indigo-600">{{ totalPts }}</p>
                                <p class="text-xs text-gray-500">pts ganados</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p
                                    class="text-sm font-bold leading-tight"
                                    :class="currentAchievement ? STAGE_COLORS[currentAchievement.color].text : 'text-gray-400'"
                                >
                                    {{ currentAchievement ? getDisplayName(currentAchievement) : 'Sin logro' }}
                                </p>
                                <p class="text-xs text-gray-500">logro actual</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <template v-if="nextAchievement">
                                    <p class="text-lg font-bold text-gray-700">{{ pointsToNext }}</p>
                                    <p class="text-xs text-gray-500">pts para {{ getDisplayName(nextAchievement) }}</p>
                                </template>
                                <template v-else>
                                    <p class="text-lg">🏆</p>
                                    <p class="text-xs font-medium text-red-500">¡Nivel máximo!</p>
                                </template>
                            </div>
                        </div>

                        <!-- Lista de logros + barra lateral de progreso -->
                        <div class="flex gap-3">
                            <!-- Barra lateral con avatar (se estira al alto de la lista) -->
                            <div class="relative flex w-8 shrink-0 justify-center">
                                <!-- Track gris (ocupa todo el alto) -->
                                <div class="absolute inset-y-0 w-1 rounded-full bg-gray-200" />
                                <!-- Relleno de progreso desde arriba -->
                                <div
                                    class="absolute top-0 w-1 rounded-full transition-all duration-700"
                                    :class="currentAchievement ? STAGE_COLORS[currentAchievement.color].badge : 'bg-gray-300'"
                                    :style="{ height: progressFillPercent + '%' }"
                                />
                                <!-- Avatar posicionado en la fila del logro actual -->
                                <div
                                    class="absolute z-10 -translate-x-1/2 -translate-y-1/2 transition-all duration-700"
                                    style="left: 50%"
                                    :style="{ top: avatarTopPercent + '%' }"
                                >
                                    <div class="rounded-full ring-2 ring-white shadow-md">
                                        <ChildIcon
                                            :icon="child.icon"
                                            :avatar-url="child.avatar_url"
                                            size="sm"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Lista con altura uniforme (10 → 500, de arriba hacia abajo) -->
                            <div class="flex-1 space-y-1.5">
                                <div
                                    v-for="achievement in achievementsAsc"
                                    :key="achievement.points"
                                    class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm transition"
                                    :class="[
                                        currentAchievement?.points === achievement.points
                                            ? STAGE_COLORS[achievement.color].bg + ' ' + STAGE_COLORS[achievement.color].border + ' ring-2 ' + STAGE_COLORS[achievement.color].ring
                                            : totalPts >= achievement.points
                                                ? STAGE_COLORS[achievement.color].bg + ' ' + STAGE_COLORS[achievement.color].border
                                                : 'border-gray-200 bg-gray-50'
                                    ]"
                                >
                                    <!-- Estado: check o candado -->
                                    <span class="shrink-0 text-base leading-none">
                                        {{ totalPts >= achievement.points ? '✅' : '🔒' }}
                                    </span>
                                    <!-- Nombre del logro -->
                                    <span
                                        class="flex-1 font-medium"
                                        :class="totalPts >= achievement.points ? STAGE_COLORS[achievement.color].text : 'text-gray-400'"
                                    >
                                        {{ getDisplayName(achievement) }}
                                    </span>
                                    <!-- Puntos requeridos -->
                                    <span
                                        class="shrink-0 text-xs font-semibold"
                                        :class="totalPts >= achievement.points ? STAGE_COLORS[achievement.color].text : 'text-gray-400'"
                                    >
                                        {{ achievement.points }}
                                    </span>
                                    <!-- Ícono del nivel -->
                                    <div class="relative shrink-0">
                                        <img
                                            :src="iconUrl(achievement.level)"
                                            :alt="achievement.name"
                                            class="h-10 w-10 rounded-lg object-cover transition-all duration-300"
                                            :class="totalPts >= achievement.points ? '' : 'opacity-60'"
                                        />
                                        <span
                                            v-if="totalPts >= achievement.points"
                                            class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-green-500 text-xs text-white shadow"
                                        >✓</span>
                                        <span
                                            v-else
                                            class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-gray-400 text-xs text-white shadow"
                                        >🔒</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <p class="mt-4 text-center text-xs text-gray-400">
                Solo lectura · Chanta Puntos
            </p>
        </div>
    </div>
</template>
