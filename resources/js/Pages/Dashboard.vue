<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ChildIcon from '@/Components/ChildIcon.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { resizeImageFile } from '@/utils/resizeImage';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

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

const sharingChildId = ref(null);
async function shareChild(child) {
    sharingChildId.value = child.id;
    try {
        const res = await fetch(route('children.share-url', child.id));
        const data = await res.json();
        if (data.whatsapp_url) window.open(data.whatsapp_url, '_blank');
    } finally {
        sharingChildId.value = null;
    }
}

const installPromptEvent = ref(null);
const showInstallButton = ref(false);
const isStandalone = ref(false);

onMounted(() => {
    isStandalone.value =
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        installPromptEvent.value = e;
        showInstallButton.value = true;
    });
});

function installApp() {
    if (!installPromptEvent.value) return;
    installPromptEvent.value.prompt();
    installPromptEvent.value.userChoice.then(() => {
        showInstallButton.value = false;
        installPromptEvent.value = null;
    });
}

function shareApp() {
    const url = window.location.origin + '/';
    const text = 'Te recomiendo Chanta Puntos, una app para mejorar la relación con tus hijos y su participación en las tareas de la casa: ' + url;
    window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
}
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
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Invitar a otro padre
                </Link>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <div class="mb-6 flex flex-col items-center gap-3">
                    <button
                        type="button"
                        class="flex w-full max-w-md items-center justify-center gap-2 rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        @click="openAdd"
                    >
                        <span aria-hidden="true">😊</span>
                        Sumar puntos
                    </button>
                    <button
                        type="button"
                        class="flex w-full max-w-md items-center justify-center gap-2 rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        @click="openSubtract"
                    >
                        <span aria-hidden="true">😢</span>
                        Restar puntos
                    </button>
                    <Link
                        v-if="canRedeem"
                        :href="route('redemptions.create')"
                        class="flex w-full max-w-md items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <span class="text-xl" aria-hidden="true">💰</span>
                        Canje de puntos
                    </Link>
                    <div
                        v-else
                        class="flex w-full max-w-md cursor-not-allowed items-center justify-center gap-2 rounded-md border border-gray-200 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400"
                        title="Ningún hijo tiene puntos para canjear"
                    >
                        <span class="text-xl" aria-hidden="true">💰</span>
                        Canje de puntos
                    </div>
                    <div
                        v-if="showInstallButton && !isStandalone"
                        class="flex w-full max-w-md items-center justify-between gap-3 rounded-md border border-indigo-200 bg-indigo-50 px-4 py-3"
                    >
                        <span class="text-sm font-medium text-indigo-800">
                            Instalar Chanta Puntos en tu teléfono
                        </span>
                        <PrimaryButton type="button" class="shrink-0" @click="installApp">
                            Instalar
                        </PrimaryButton>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <h3 class="mb-4 text-sm font-medium text-gray-700">
                            Puntos por hijo
                        </h3>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="child in children"
                                :key="child.id"
                                class="flex items-center gap-4 rounded-xl border border-gray-200 bg-gray-50 p-4"
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
                                        size="lg"
                                    />
                                </button>
                                <div
                                    v-else
                                    class="flex shrink-0"
                                >
                                    <ChildIcon
                                        :icon="child.icon"
                                        :avatar-url="child.avatar_url"
                                        size="lg"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <button
                                        v-if="child.is_owner"
                                        type="button"
                                        class="cursor-pointer text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded"
                                        @click="openEditChild(child)"
                                    >
                                        <p class="truncate font-medium text-gray-900 hover:text-indigo-600">
                                            {{ child.name }}
                                        </p>
                                    </button>
                                    <p
                                        v-else
                                        class="truncate font-medium text-gray-900"
                                    >
                                        {{ child.name }}
                                    </p>
                                    <p
                                        class="text-2xl font-bold"
                                        :class="child.points >= 0 ? 'text-indigo-600' : 'text-red-600'"
                                    >
                                        {{ child.points }} pts
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="flex shrink-0 rounded p-2 text-green-600 transition hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    title="Compartir ficha por WhatsApp"
                                    aria-label="Compartir por WhatsApp"
                                    :disabled="sharingChildId === child.id"
                                    @click="shareChild(child)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex w-full max-w-md flex-col items-center rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:mx-0">
                    <button
                        type="button"
                        class="flex w-full max-w-md items-center justify-center gap-2 rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
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
