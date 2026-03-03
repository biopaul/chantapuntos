<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ChildIcon from '@/Components/ChildIcon.vue';
import Modal from '@/Components/Modal.vue';
import Skeleton from '@/Components/Skeleton.vue';
import { resizeImageFile } from '@/utils/resizeImage';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    children: { type: Array, required: true },
    canAddChild: { type: Boolean, default: true },
    availableIcons: { type: Array, required: true },
});

const showContent = ref(false);
onMounted(() => {
    requestAnimationFrame(() => {
        setTimeout(() => {
            showContent.value = true;
        }, 220);
    });
});

const form = useForm({
    name: '',
    icon: 'star',
    avatar: null,
});

const editForm = useForm({
    name: '',
    icon: 'star',
    avatar: null,
});

const avatarPreview = ref(null);
const editAvatarPreview = ref(null);
const showEditModal = ref(false);
const editingChild = ref(null);
const fileInputRef = ref(null);
const cameraInputRef = ref(null);
const editFileInputRef = ref(null);
const editCameraInputRef = ref(null);

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

function getAvatarUrl(path) {
    if (!path) return null;
    return `/storage/${path}`;
}
function getChildAvatarUrl(child) {
    return child.avatar_url ?? getAvatarUrl(child.avatar_path);
}

async function onAvatarChange(e) {
    const file = e.target.files?.[0];
    if (!file) {
        form.avatar = null;
        avatarPreview.value = null;
        return;
    }
    const resized = await resizeImageFile(file);
    form.avatar = resized;
    avatarPreview.value = URL.createObjectURL(resized);
    e.target.value = '';
}

function selectIcon(id) {
    form.icon = id;
}

function submit() {
    form.post(route('children.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name', 'icon', 'avatar');
            form.avatar = null;
            avatarPreview.value = null;
        },
    });
}

function openEdit(child) {
    editingChild.value = child;
    editForm.name = child.name;
    editForm.icon = child.icon ?? 'star';
    editForm.avatar = null;
    editAvatarPreview.value = getChildAvatarUrl(child);
    showEditModal.value = true;
}

async function onEditAvatarChange(e) {
    const file = e.target.files?.[0];
    if (!file) {
        editForm.avatar = null;
        editAvatarPreview.value = editingChild.value ? getChildAvatarUrl(editingChild.value) : null;
        return;
    }
    const resized = await resizeImageFile(file);
    editForm.avatar = resized;
    editAvatarPreview.value = URL.createObjectURL(resized);
    e.target.value = '';
}

function submitEdit() {
    if (!editingChild.value) return;
    editForm.transform((data) => ({ ...data, _method: 'put' })).post(route('children.update', editingChild.value), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            editingChild.value = null;
        },
    });
}
</script>

<template>
    <Head title="Añade a tus hijos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Añade al menos un hijo
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <template v-if="!showContent">
                            <div class="mb-6 space-y-2">
                                <Skeleton variant="line" class="w-full" />
                                <Skeleton variant="line" class="w-5/6" />
                            </div>
                            <div class="mb-8 space-y-2">
                                <Skeleton variant="line" class="mb-3 h-4 w-24" />
                                <div
                                    v-for="n in 3"
                                    :key="'sk-' + n"
                                    class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3"
                                >
                                    <Skeleton variant="circle" class="h-10 w-10" />
                                    <Skeleton variant="line" class="h-5 w-28" />
                                    <Skeleton variant="line" class="h-9 w-20" />
                                </div>
                            </div>
                            <div class="space-y-4">
                                <Skeleton variant="line" class="h-10 w-full" />
                                <Skeleton variant="line" class="h-10 w-full" />
                                <Skeleton variant="line" class="h-10 w-32" />
                            </div>
                        </template>
                        <template v-else>
                        <p class="mb-6 text-gray-600">
                            Elige un nombre, un ícono o sube una foto para cada hijo.
                        </p>

                        <div v-if="children.length > 0" class="mb-8">
                            <h3 class="mb-3 text-sm font-medium text-gray-700">
                                Tus hijos
                            </h3>
                            <ul class="space-y-2">
                                <li
                                    v-for="child in children"
                                    :key="child.id"
                                    class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3"
                                >
                                    <div class="flex items-center gap-3">
                                        <ChildIcon
                                            :icon="child.icon"
                                            :avatar-url="getChildAvatarUrl(child)"
                                            size="sm"
                                        />
                                        <span class="font-medium text-gray-800">{{ child.name }}</span>
                                    </div>
                                    <SecondaryButton
                                        v-if="child.is_owner"
                                        type="button"
                                        class="w-full sm:w-auto"
                                        @click="openEdit(child)"
                                    >
                                        Editar
                                    </SecondaryButton>
                                </li>
                            </ul>
                            <div class="mt-4">
                                <Link
                                    :href="route('dashboard')"
                                    as="button"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-gray-800 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Continuar al inicio
                                </Link>
                            </div>
                        </div>

                        <form v-if="canAddChild" @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nombre" />
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                    maxlength="255"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel value="Ícono" />
                                <div class="mt-2 grid grid-cols-8 gap-2">
                                    <button
                                        v-for="opt in availableIcons"
                                        :key="opt.id"
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-full text-2xl transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        :class="{
                                            'ring-2 ring-indigo-500 ring-offset-2': form.icon === opt.id,
                                        }"
                                        :title="opt.label"
                                        @click="selectIcon(opt.id)"
                                    >
                                        {{ iconEmoji[opt.id] ?? '⭐' }}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <InputLabel value="Foto (opcional)" />
                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                    <div
                                        v-if="avatarPreview"
                                        class="h-16 w-16 overflow-hidden rounded-full border-2 border-gray-200"
                                    >
                                        <img :src="avatarPreview" alt="Vista previa" class="h-full w-full object-cover" />
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <input
                                            ref="cameraInputRef"
                                            type="file"
                                            accept="image/*"
                                            capture="environment"
                                            class="hidden"
                                            @change="onAvatarChange"
                                        />
                                        <input
                                            ref="fileInputRef"
                                            type="file"
                                            accept="image/*"
                                            class="hidden"
                                            @change="onAvatarChange"
                                        />
                                        <SecondaryButton type="button" @click="cameraInputRef?.click()">
                                            Sacar foto
                                        </SecondaryButton>
                                        <SecondaryButton type="button" @click="fileInputRef?.click()">
                                            Elegir de galería
                                        </SecondaryButton>
                                    </div>
                                </div>
                                <InputError :message="form.errors.avatar" />
                            </div>

                            <div class="flex flex-col gap-3">
                                <PrimaryButton type="submit" class="w-full" :disabled="form.processing">
                                    Añadir hijo
                                </PrimaryButton>
                                <Link
                                    v-if="children.length > 0"
                                    :href="route('dashboard')"
                                    as="button"
                                    class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Continuar sin añadir más
                                </Link>
                            </div>
                        </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Editar hijo</h3>
                <form v-if="editingChild" @submit.prevent="submitEdit" class="mt-4 space-y-6">
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
                                <SecondaryButton type="button" @click="editCameraInputRef?.click()">
                                    Sacar foto
                                </SecondaryButton>
                                <SecondaryButton type="button" @click="editFileInputRef?.click()">
                                    Elegir de galería
                                </SecondaryButton>
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
