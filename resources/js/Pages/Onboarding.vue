<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ChildIcon from '@/Components/ChildIcon.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    children: { type: Array, required: true },
    canAddChild: { type: Boolean, default: true },
    availableIcons: { type: Array, required: true },
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

function onAvatarChange(e) {
    const file = e.target.files?.[0];
    form.avatar = file ?? null;
    avatarPreview.value = file ? URL.createObjectURL(file) : null;
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

function onEditAvatarChange(e) {
    const file = e.target.files?.[0];
    editForm.avatar = file ?? null;
    editAvatarPreview.value = file ? URL.createObjectURL(file) : (editingChild.value ? getChildAvatarUrl(editingChild.value) : null);
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

        <div class="py-8">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
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
                                    <button
                                        v-if="child.is_owner"
                                        type="button"
                                        class="text-sm text-indigo-600 hover:text-indigo-800"
                                        @click="openEdit(child)"
                                    >
                                        Editar
                                    </button>
                                </li>
                            </ul>
                            <div class="mt-4">
                                <Link
                                    :href="route('dashboard')"
                                    as="button"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700"
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
                                <div class="mt-2 flex items-center gap-4">
                                    <div
                                        v-if="avatarPreview"
                                        class="h-16 w-16 overflow-hidden rounded-full border-2 border-gray-200"
                                    >
                                        <img :src="avatarPreview" alt="Vista previa" class="h-full w-full object-cover" />
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 hover:file:bg-indigo-100"
                                        @change="onAvatarChange"
                                    />
                                </div>
                                <InputError :message="form.errors.avatar" />
                            </div>

                            <div class="flex gap-3">
                                <PrimaryButton type="submit" :disabled="form.processing">
                                    Añadir hijo
                                </PrimaryButton>
                                <Link
                                    v-if="children.length > 0"
                                    :href="route('dashboard')"
                                    as="button"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                >
                                    Continuar sin añadir más
                                </Link>
                            </div>
                        </form>
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
                        <div class="mt-2 flex items-center gap-4">
                            <div
                                v-if="editAvatarPreview"
                                class="h-16 w-16 overflow-hidden rounded-full border-2 border-gray-200"
                            >
                                <img :src="editAvatarPreview" alt="Vista previa" class="h-full w-full object-cover" />
                            </div>
                            <input
                                type="file"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 hover:file:bg-indigo-100"
                                @change="onEditAvatarChange"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Sube una nueva imagen para reemplazar la actual.</p>
                        <InputError :message="editForm.errors.avatar" />
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            @click="showEditModal = false"
                        >
                            Cancelar
                        </button>
                        <PrimaryButton type="submit" :disabled="editForm.processing">
                            Guardar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
