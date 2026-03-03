<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ChildIcon from '@/Components/ChildIcon.vue';
import {
    ACHIEVEMENTS,
    getCurrentAchievement,
    getNextAchievement,
    getProgressPercent,
    getPointsToNext,
    getDisplayName,
    STAGE_COLORS,
} from '@/utils/achievements';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    children: { type: Array, required: true },
});

const STAGE_ORDER = ['Modo Chanta', 'Despertando Llama', 'Modo Llama', 'Llama Legendaria'];

const stages = computed(() => {
    return STAGE_ORDER.map((stageName) => ({
        name: stageName,
        achievements: ACHIEVEMENTS.filter((a) => a.stage === stageName),
        color: ACHIEVEMENTS.find((a) => a.stage === stageName)?.color ?? 'yellow',
    }));
});

function isUnlocked(achievement, totalPts) {
    return (totalPts ?? 0) >= achievement.points;
}

function isCurrent(achievement, totalPts) {
    const current = getCurrentAchievement(totalPts ?? 0);
    return current?.points === achievement.points;
}

// Lee la URL base desde el meta tag inyectado por Laravel en app.blade.php
// Garantiza la URL correcta sin importar la configuración del entorno
const assetBase = document.head
    .querySelector('meta[name="asset-url"]')
    ?.getAttribute('content')
    ?.replace(/\/$/, '') ?? '';

function iconUrl(level) {
    return `${assetBase}/images/achievements/${level}.png`;
}

const stageEmoji = {
    'Modo Chanta': '🟡',
    'Despertando Llama': '🟠',
    'Modo Llama': '🔵',
    'Llama Legendaria': '🔴',
};

// Computed map: child.id → { pts, current, next, progress, toNext }
const childrenData = computed(() => {
    const map = new Map();
    for (const child of props.children) {
        const pts = child.total_points_earned ?? 0;
        map.set(child.id, {
            pts,
            current: getCurrentAchievement(pts),
            next: getNextAchievement(pts),
            progress: getProgressPercent(pts),
            toNext: getPointsToNext(pts),
        });
    }
    return map;
});

// Máximo pts ganados entre todos los hijos (para el ✅/🔒 global)
const maxPtsEarned = computed(() =>
    props.children.reduce((acc, c) => Math.max(acc, c.total_points_earned ?? 0), 0)
);
</script>

<template>
    <Head title="Logros" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <span class="text-2xl" aria-hidden="true">🏆</span>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Logros</h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

                <!-- Resumen por hijo -->
                <div v-if="children.length > 0" class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="child in children"
                        :key="child.id"
                        class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                    >
                        <ChildIcon :icon="child.icon" :avatar-url="child.avatar_url" size="lg" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-gray-900">{{ child.name }}</p>
                            <template v-if="childrenData.get(child.id).current">
                                <p
                                    class="mt-0.5 text-sm font-medium"
                                    :class="STAGE_COLORS[childrenData.get(child.id).current.color].text"
                                >
                                    {{ getDisplayName(childrenData.get(child.id).current) }}
                                </p>
                                <p class="text-xs text-gray-500">{{ childrenData.get(child.id).current.stage }}</p>
                            </template>
                            <template v-else>
                                <p class="mt-0.5 text-sm text-gray-400">Sin logro aún</p>
                                <p class="text-xs text-gray-400">{{ childrenData.get(child.id).pts }} / 10 pts para empezar</p>
                            </template>
                            <!-- Barra de progreso -->
                            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200">
                                <div
                                    class="h-full rounded-full transition-all duration-500"
                                    :class="childrenData.get(child.id).current ? STAGE_COLORS[childrenData.get(child.id).current.color].badge : 'bg-gray-400'"
                                    :style="{ width: childrenData.get(child.id).progress + '%' }"
                                />
                            </div>
                            <p v-if="childrenData.get(child.id).next" class="mt-1 text-xs text-gray-400">
                                Próximo: {{ getDisplayName(childrenData.get(child.id).next) }} (faltan {{ childrenData.get(child.id).toNext }} pts)
                            </p>
                            <p v-else class="mt-1 text-xs font-medium text-red-600">
                                ¡Nivel máximo! 🏆
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tabla de logros por etapa -->
                <div class="space-y-6">
                    <div
                        v-for="stage in stages"
                        :key="stage.name"
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                    >
                        <!-- Encabezado de etapa -->
                        <div
                            class="flex items-center gap-3 border-b px-5 py-4"
                            :class="STAGE_COLORS[stage.color].bg + ' ' + STAGE_COLORS[stage.color].border"
                        >
                            <span class="text-xl" aria-hidden="true">{{ stageEmoji[stage.name] }}</span>
                            <h3 class="text-base font-semibold" :class="STAGE_COLORS[stage.color].text">
                                {{ stage.name }}
                            </h3>
                        </div>

                        <!-- Logros de la etapa -->
                        <div class="divide-y divide-gray-100">
                            <div
                                v-for="achievement in stage.achievements"
                                :key="achievement.points"
                                class="px-5 py-4"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <!-- Info del logro -->
                                    <div class="flex items-center gap-3">
                                        <!-- Ícono del logro -->
                                        <div class="relative shrink-0">
                                            <img
                                                :src="iconUrl(achievement.level)"
                                                :alt="achievement.name"
                                                class="h-14 w-14 rounded-xl object-cover transition-all duration-300"
                                                :class="achievement.points <= maxPtsEarned ? '' : 'opacity-60'"
                                            />
                                            <!-- Badge de estado: check verde si desbloqueado, candado si bloqueado -->
                                            <span
                                                v-if="achievement.points <= maxPtsEarned"
                                                class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-xs text-white shadow"
                                                aria-label="Desbloqueado"
                                            >✓</span>
                                            <span
                                                v-else
                                                class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-gray-400 text-xs text-white shadow"
                                                aria-label="Bloqueado"
                                            >🔒</span>
                                        </div>
                                        <div>
                                            <p
                                                class="font-medium"
                                                :class="achievement.points <= maxPtsEarned ? 'text-gray-900' : 'text-gray-400'"
                                            >{{ getDisplayName(achievement) }}</p>
                                            <p class="text-xs text-gray-500">{{ achievement.points }} pts acumulados</p>
                                        </div>
                                    </div>

                                    <!-- Estado por hijo -->
                                    <div class="flex shrink-0 flex-wrap justify-end gap-2">
                                        <div
                                            v-for="child in children"
                                            :key="child.id"
                                            class="flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-xs font-medium transition"
                                            :class="[
                                                isCurrent(achievement, child.total_points_earned)
                                                    ? STAGE_COLORS[achievement.color].bg + ' ' + STAGE_COLORS[achievement.color].text + ' ' + STAGE_COLORS[achievement.color].border + ' ring-2 ' + STAGE_COLORS[achievement.color].ring
                                                    : isUnlocked(achievement, child.total_points_earned)
                                                        ? STAGE_COLORS[achievement.color].bg + ' ' + STAGE_COLORS[achievement.color].text + ' ' + STAGE_COLORS[achievement.color].border
                                                        : 'border-gray-200 bg-gray-50 text-gray-400'
                                            ]"
                                        >
                                            <span
                                                class="h-1.5 w-1.5 rounded-full shrink-0"
                                                :class="isCurrent(achievement, child.total_points_earned) || isUnlocked(achievement, child.total_points_earned)
                                                    ? STAGE_COLORS[achievement.color].dot
                                                    : 'bg-gray-300'"
                                            />
                                            {{ child.name }}
                                            <span v-if="isCurrent(achievement, child.total_points_earned)" aria-label="Logro actual">★</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volver al inicio -->
                <div class="mt-8 text-center">
                    <Link
                        :href="route('dashboard')"
                        class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline"
                    >
                        ← Volver al inicio
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
