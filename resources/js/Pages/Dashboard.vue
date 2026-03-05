<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ChildIcon from '@/Components/ChildIcon.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Skeleton from '@/Components/Skeleton.vue';
import { resizeImageFile } from '@/utils/resizeImage';
import {
    getCurrentAchievement,
    getNextAchievement,
    getProgressPercent,
    getPointsToNext,
    getDisplayName,
    STAGE_COLORS,
} from '@/utils/achievements';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, onUnmounted, reactive, ref } from 'vue';

const props = defineProps({
    children: { type: Array, required: true },
    actionsPositive: { type: Array, default: () => [] },
    actionsNegative: { type: Array, default: () => [] },
    canRedeem: { type: Boolean, default: false },
    canInvite: { type: Boolean, default: false },
    availableIcons: { type: Array, default: () => [] },
});

const iconEmoji = {
    star: '⭐',
    heart: '❤️',
    sun: '☀️',
    moon: '🌙',
    cat: '🐱',
    dog: '🐕',
    fish: '🐟',
    bird: '🐦',
    balloon: '🎈',
    gift: '🎁',
    cake: '🎂',
    music: '🎵',
    book: '📖',
    pencil: '✏️',
    flower: '🌸',
    rainbow: '🌈',
};

const showAddModal = ref(false);
const showSubtractModal = ref(false);
const showEditModal = ref(false);
const editingChild = ref(null);
const addForm = useForm({ child_id: '', action_id: '' });
const subtractForm = useForm({ child_id: '', action_id: '' });
const editForm = useForm({ name: '', icon: 'star', avatar: null });
const editAvatarPreview = ref(null);
const editFileInputRef = ref(null);
const editCameraInputRef = ref(null);


function openAdd() {
    addForm.reset();
    showAddModal.value = true;
}

function openSubtract() {
    subtractForm.reset();
    showSubtractModal.value = true;
}

function submitAdd() {
    addForm.post(route('points.task'), {
        preserveScroll: true,
        onSuccess: () => showAddModal.value = false,
    });
}

function submitSubtract() {
    subtractForm.post(route('points.task'), {
        preserveScroll: true,
        onSuccess: () => showSubtractModal.value = false,
    });
}

function openEditChild(child) {
    editingChild.value = child;
    editForm.name = child.name;
    editForm.icon = child.icon ?? 'star';
    editForm.avatar = null;
    editForm.clearErrors();
    editAvatarPreview.value = child.avatar_url ?? null;
    showEditModal.value = true;
}

async function onEditAvatarChange(e) {
    const file = e.target.files?.[0];
    if (!file) {
        editForm.avatar = null;
        editAvatarPreview.value = editingChild.value?.avatar_url ?? null;
        return;
    }
    const resized = await resizeImageFile(file);
    editForm.avatar = resized;
    editAvatarPreview.value = URL.createObjectURL(resized);
    e.target.value = '';
}

function submitEditChild() {
    if (!editingChild.value) return;
    editForm.transform((data) => ({ ...data, _method: 'put' })).post(route('children.update', editingChild.value), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            editingChild.value = null;
        },
    });
}

const showContent = ref(false);
onMounted(() => {
    requestAnimationFrame(() => {
        setTimeout(() => {
            showContent.value = true;
        }, 220);
    });
});

const sharingChildId = ref(null);
function shareChild(child) {
    const url = child.whatsapp_share_url;
    if (!url) return;
    sharingChildId.value = child.id;
    // Usar location.href en lugar de window.open para que funcione en móvil (no se bloquea como popup)
    window.location.href = url;
    sharingChildId.value = null;
}

function shareApp() {
    const url = window.location.origin + '/';
    const text = 'Te recomiendo Chanta Puntos, una app para mejorar la relación con tus hijos y su participación en las tareas de la casa: ' + url;
    window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
}

// --- Flip cards ---
const revealedSet = reactive(new Set());
const revealTimers = {};

function revealCard(childId) {
    revealedSet.add(childId);
    if (revealTimers[childId]) clearTimeout(revealTimers[childId]);
    revealTimers[childId] = setTimeout(() => hideCard(childId), 15000);
}

function hideCard(childId) {
    revealedSet.delete(childId);
    if (revealTimers[childId]) {
        clearTimeout(revealTimers[childId]);
        delete revealTimers[childId];
    }
}

const isRevealed = (childId) => revealedSet.has(childId);

onUnmounted(() => {
    Object.values(revealTimers).forEach(clearTimeout);
});
</script>

<template>
    <Head title="Inicio" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Hogar
                </h2>
                <Link
                    v-if="canInvite"
                    :href="route('invitations.index')"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Invitar padre
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-4xl">
                <div class="mb-6 flex flex-col items-center gap-3">
                    <!-- Fila Sumar (~75%) + Restar (~25%), más altos que el resto -->
                    <div class="flex w-full max-w-md gap-2">
                        <button
                            type="button"
                            class="flex flex-[3] items-center justify-center gap-2 rounded-md border border-transparent bg-green-600 px-3 py-5 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            @click="openAdd"
                        >
                            <span aria-hidden="true">😊</span>
                            Sumar puntos
                        </button>
                        <button
                            type="button"
                            class="flex flex-[2] flex-col items-center justify-center gap-0.5 rounded-md border border-transparent bg-red-600 py-5 text-xs font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            @click="openSubtract"
                        >
                            <span aria-hidden="true" class="text-base leading-none">😢</span>
                            <span class="leading-tight">Restar</span>
                        </button>
                    </div>
                    <Link
                        v-if="canRedeem"
                        :href="route('redemptions.create')"
                        class="flex w-full max-w-md items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <span class="text-xl" aria-hidden="true">💰</span>
                        Canje de puntos
                    </Link>
                    <div
                        v-else
                        class="flex w-full max-w-md cursor-not-allowed items-center justify-center gap-2 rounded-md border border-gray-200 bg-gray-100 px-5 py-3 text-sm font-medium text-gray-400"
                        title="Ningún hijo tiene puntos para canjear"
                    >
                        <span class="text-xl" aria-hidden="true">💰</span>
                        Canje de puntos
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <template v-if="!showContent">
                        <div
                            v-for="n in 6"
                            :key="'skeleton-' + n"
                            class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-md"
                        >
                            <Skeleton variant="circle" class="h-20 w-20 shrink-0" />
                            <div class="min-w-0 flex-1 space-y-2">
                                <Skeleton variant="line" class="w-28 h-5" />
                                <Skeleton variant="line" class="w-16" />
                                <Skeleton variant="line" class="w-28" />
                            </div>
                            <Skeleton variant="circle" class="h-10 w-10 shrink-0" />
                        </div>
                    </template>
                    <!-- Wrapper de perspectiva por tarjeta -->
                    <div
                        v-else
                        v-for="child in children"
                        :key="child.id"
                        style="perspective: 800px"
                    >
                        <Transition name="flip" mode="out-in">
                            <!-- CARA OCULTA: misma estructura que revelada + franja celeste absoluta -->
                            <div
                                v-if="!isRevealed(child.id)"
                                key="hidden"
                                class="relative overflow-hidden rounded-2xl bg-white shadow-md cursor-pointer"
                                :aria-label="'Ver puntos de ' + child.name"
                                @click="revealCard(child.id)"
                            >
                                <div class="flex items-center gap-4 px-4 pt-4 pb-12">
                                    <!-- Avatar/ícono real del hijo -->
                                    <div class="flex shrink-0">
                                        <ChildIcon
                                            :icon="child.icon"
                                            :avatar-url="child.avatar_url"
                                            size="xl"
                                        />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <!-- Fila nombre + ícono de ojo (misma estructura que la fila nombre+WA) -->
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="min-w-0 truncate text-lg font-bold text-gray-900">{{ child.name }}</p>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                        <!-- Skeleton de puntos -->
                                        <div class="mt-0.5 h-7 w-20 rounded-md bg-gray-200 animate-pulse" />
                                        <!-- Skeleton de logro -->
                                        <div class="mt-1 h-3 w-40 rounded bg-gray-200 animate-pulse" />
                                        <!-- Skeleton de barra de progreso -->
                                        <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 animate-pulse" />
                                        <!-- Espacio para la siguiente línea (igual que "Faltan X pts") -->
                                        <div class="mt-0.5 h-3 w-32 rounded bg-gray-200 animate-pulse" />
                                    </div>
                                </div>
                                <!-- Franja celeste absolutamente posicionada al fondo -->
                                <div class="absolute inset-x-0 bottom-0 flex items-center gap-2 bg-cyan-500 px-4 py-2.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span class="text-sm font-medium text-white">Tocá para ver los puntos</span>
                                </div>
                            </div>

                            <!-- CARA REVELADA: pb-12 igual que oculta para altura idéntica -->
                            <div
                                v-else
                                key="revealed"
                                class="flex items-center gap-4 rounded-2xl bg-white px-4 pt-4 pb-12 shadow-md"
                            >
                                <button
                                    v-if="child.is_owner"
                                    type="button"
                                    class="flex shrink-0 cursor-pointer rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    :aria-label="'Editar ' + child.name"
                                    @click="openEditChild(child)"
                                >
                                    <ChildIcon
                                        :icon="child.icon"
                                        :avatar-url="child.avatar_url"
                                        size="xl"
                                    />
                                </button>
                                <div v-else class="flex shrink-0">
                                    <ChildIcon
                                        :icon="child.icon"
                                        :avatar-url="child.avatar_url"
                                        size="xl"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <!-- Nombre + botones WhatsApp y ocultar en la misma línea -->
                                    <div class="flex items-center justify-between gap-2">
                                        <button
                                            v-if="child.is_owner"
                                            type="button"
                                            class="min-w-0 cursor-pointer text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded"
                                            @click="openEditChild(child)"
                                        >
                                            <p class="truncate text-lg font-bold text-gray-900 hover:text-indigo-600">
                                                {{ child.name }}
                                            </p>
                                        </button>
                                        <p v-else class="min-w-0 truncate text-lg font-bold text-gray-900">
                                            {{ child.name }}
                                        </p>
                                        <div class="flex shrink-0 items-center gap-1">
                                            <!-- Botón ocultar puntos -->
                                            <button
                                                type="button"
                                                class="rounded p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400"
                                                title="Ocultar puntos"
                                                aria-label="Ocultar puntos"
                                                @click.stop="hideCard(child.id)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                            <!-- Botón WhatsApp -->
                                            <button
                                                type="button"
                                                class="rounded p-1 text-green-600 transition hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                                                title="Compartir ficha por WhatsApp"
                                                aria-label="Compartir por WhatsApp"
                                                :disabled="sharingChildId === child.id"
                                                @click.stop="shareChild(child)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p
                                        class="text-2xl font-bold leading-tight"
                                        :class="child.points >= 0 ? 'text-indigo-600' : 'text-red-600'"
                                    >
                                        {{ child.points }} pts
                                    </p>
                                    <!-- Pts ganados | Logro actual — en la misma línea -->
                                    <p class="text-xs">
                                        <span class="text-gray-400">{{ child.total_points_earned ?? 0 }} puntos totales ganados</span>
                                        <template v-if="getCurrentAchievement(child.total_points_earned)">
                                            <span class="mx-1 text-gray-300">|</span>
                                            <span
                                                class="font-semibold"
                                                :class="STAGE_COLORS[getCurrentAchievement(child.total_points_earned).color].text"
                                            >🏆 {{ getDisplayName(getCurrentAchievement(child.total_points_earned)) }}</span>
                                        </template>
                                        <template v-else>
                                            <span class="mx-1 text-gray-300">|</span>
                                            <span class="text-gray-400">Sin logro aún</span>
                                        </template>
                                    </p>
                                    <!-- Barra de progreso hacia el próximo logro -->
                                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-200">
                                        <div
                                            class="h-full rounded-full transition-all duration-500"
                                            :class="getCurrentAchievement(child.total_points_earned)
                                                ? STAGE_COLORS[getCurrentAchievement(child.total_points_earned).color].badge
                                                : 'bg-gray-400'"
                                            :style="{ width: getProgressPercent(child.total_points_earned) + '%' }"
                                        />
                                    </div>
                                    <p v-if="getNextAchievement(child.total_points_earned)" class="mt-0.5 text-xs text-gray-400">
                                        Faltan {{ getPointsToNext(child.total_points_earned) }} pts para Nivel {{ getNextAchievement(child.total_points_earned).level }}
                                    </p>
                                    <p v-else class="mt-0.5 text-xs font-medium text-red-500">¡Nivel máximo! 🏆</p>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Botón Logros: sobre fondo gris, igual que Sumar/Restar/Canje -->
                <div class="mt-4 flex justify-center">
                    <Link
                        :href="route('achievements.index')"
                        class="flex w-full max-w-md items-center justify-center gap-2 rounded-md border border-transparent bg-yellow-500 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.98 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.673z"/>
                        </svg>
                        Logros
                    </Link>
                </div>

                <!-- Bloque Compartí: tarjeta ancho completo -->
                <div class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="flex flex-col items-center p-6">
                    <button
                        type="button"
                        class="flex w-full max-w-md items-center justify-center gap-2 rounded-md border border-transparent bg-green-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        @click="shareApp"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Compartí esta app
                    </button>
                    <p class="mt-4 text-center text-sm text-gray-600">
                        Si ves que te estamos ayudando a mejorar la relación con tus hijos y su participación y colaboración en las tareas de la casa, compartí esta app con tus amigos a los que creas que le puede ayudar como a vos.
                        <span class="inline-block text-red-500" aria-hidden="true">❤️</span>
                    </p>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showAddModal" @close="showAddModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Sumar puntos</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Elige el hijo y la acción realizada.
                </p>
                <form @submit.prevent="submitAdd" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hijo</label>
                        <select
                            v-model="addForm.child_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Selecciona...</option>
                            <option
                                v-for="c in children"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                        <p v-if="addForm.errors.child_id" class="mt-1 text-sm text-red-600">
                            {{ addForm.errors.child_id }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Acción</label>
                        <select
                            v-model="addForm.action_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Selecciona...</option>
                            <option
                                v-for="a in actionsPositive"
                                :key="a.id"
                                :value="a.id"
                            >
                                {{ a.name }} (+{{ a.points }})
                            </option>
                        </select>
                        <p v-if="addForm.errors.action_id" class="mt-1 text-sm text-red-600">
                            {{ addForm.errors.action_id }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <SecondaryButton type="button" @click="showAddModal = false">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="addForm.processing">
                            Sumar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showSubtractModal" @close="showSubtractModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Restar puntos</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Elige el hijo y la acción (comportamiento negativo).
                </p>
                <form @submit.prevent="submitSubtract" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hijo</label>
                        <select
                            v-model="subtractForm.child_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Selecciona...</option>
                            <option
                                v-for="c in children"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                        <p v-if="subtractForm.errors.child_id" class="mt-1 text-sm text-red-600">
                            {{ subtractForm.errors.child_id }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Acción</label>
                        <select
                            v-model="subtractForm.action_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Selecciona...</option>
                            <option
                                v-for="a in actionsNegative"
                                :key="a.id"
                                :value="a.id"
                            >
                                {{ a.name }} ({{ a.points }})
                            </option>
                        </select>
                        <p v-if="subtractForm.errors.action_id" class="mt-1 text-sm text-red-600">
                            {{ subtractForm.errors.action_id }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <SecondaryButton type="button" @click="showSubtractModal = false">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="subtractForm.processing">
                            Restar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Editar hijo</h3>
                <form v-if="editingChild" @submit.prevent="submitEditChild" class="mt-4 space-y-6">
                    <div>
                        <InputLabel for="edit-name" value="Nombre" />
                        <input
                            id="edit-name"
                            v-model="editForm.name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                            maxlength="255"
                        />
                        <InputError :message="editForm.errors.name" />
                    </div>
                    <div>
                        <InputLabel value="Ícono" />
                        <div class="mt-2 grid grid-cols-8 gap-2">
                            <button
                                v-for="opt in availableIcons"
                                :key="opt.id"
                                type="button"
                                class="flex h-10 w-10 items-center justify-center rounded-full text-2xl transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                :class="{ 'ring-2 ring-indigo-500 ring-offset-2': editForm.icon === opt.id }"
                                :title="opt.label"
                                @click="editForm.icon = opt.id"
                            >
                                {{ iconEmoji[opt.id] ?? '⭐' }}
                            </button>
                        </div>
                    </div>
                    <div>
                        <InputLabel value="Foto (opcional)" />
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <div
                                v-if="editAvatarPreview"
                                class="h-16 w-16 overflow-hidden rounded-full border-2 border-gray-200"
                            >
                                <img :src="editAvatarPreview" alt="Vista previa" class="h-full w-full object-cover" />
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <input
                                    ref="editCameraInputRef"
                                    type="file"
                                    accept="image/*"
                                    capture="environment"
                                    class="hidden"
                                    @change="onEditAvatarChange"
                                />
                                <input
                                    ref="editFileInputRef"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="onEditAvatarChange"
                                />
                                <button
                                    type="button"
                                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                    @click="editCameraInputRef?.click()"
                                >
                                    Sacar foto
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                    @click="editFileInputRef?.click()"
                                >
                                    Elegir de galería
                                </button>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Sube una nueva imagen para reemplazar la actual.</p>
                        <InputError :message="editForm.errors.avatar" />
                    </div>
                    <div class="flex justify-end gap-2">
                        <SecondaryButton type="button" @click="showEditModal = false">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="editForm.processing">
                            Guardar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Cara oculta sale girada hacia la derecha */
.flip-leave-active {
    transition: transform 0.22s ease-in, opacity 0.22s ease-in;
}
.flip-leave-to {
    transform: rotateY(90deg);
    opacity: 0;
}

/* Cara revelada entra desde la izquierda */
.flip-enter-active {
    transition: transform 0.22s ease-out, opacity 0.22s ease-out;
}
.flip-enter-from {
    transform: rotateY(-90deg);
    opacity: 0;
}
</style>
