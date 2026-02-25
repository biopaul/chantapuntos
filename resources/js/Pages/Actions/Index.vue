<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    actions: { type: Array, required: true },
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingAction = ref(null);

const createForm = useForm({
    name: '',
    points: 1,
});

const editForm = useForm({
    name: '',
    points: 0,
});

const pointsOptions = Array.from({ length: 21 }, (_, i) => i - 10);

function openCreate() {
    createForm.reset();
    showCreateModal.value = true;
}

function openEdit(action) {
    editingAction.value = action;
    editForm.name = action.name;
    editForm.points = action.points;
    showEditModal.value = true;
}

function submitCreate() {
    createForm.post(route('actions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
        },
    });
}

function submitEdit() {
    if (!editingAction.value) return;
    editForm.put(route('actions.update', editingAction.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            editingAction.value = null;
        },
    });
}

function isCustom(action) {
    return action.user_id != null;
}
</script>

<template>
    <Head title="Acciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Acciones
                </h2>
                <PrimaryButton @click="openCreate">
                    Nueva acción
                </PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <p class="mb-4 text-gray-600">
                            Acciones precargadas y las que añadas. Edita nombre y puntaje (-10 a 10). Las de puntaje positivo suman puntos; las negativas restan.
                        </p>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="max-w-[70%] px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                            Descripción
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                            Puntaje
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="action in actions" :key="action.id">
                                        <td class="min-w-0 max-w-[70%] px-4 py-3 text-sm text-gray-900">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="min-w-0 break-words">{{ action.name }}</span>
                                                <span
                                                    class="shrink-0 rounded px-2 py-0.5 text-xs font-medium"
                                                    :class="isCustom(action) ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                                                >
                                                    {{ isCustom(action) ? 'Personal' : 'Sistema' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <span
                                                class="inline-flex rounded-full px-2 py-1 text-xs font-medium"
                                                :class="
                                                    action.points > 0
                                                        ? 'bg-green-100 text-green-800'
                                                        : action.points < 0
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-gray-100 text-gray-800'
                                                "
                                            >
                                                {{ action.points > 0 ? '+' : '' }}{{ action.points }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    class="rounded p-1.5 text-indigo-600 transition hover:bg-indigo-50 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    title="Editar"
                                                    aria-label="Editar"
                                                    @click="openEdit(action)"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <Link
                                                    v-if="isCustom(action)"
                                                    :href="route('actions.destroy', action)"
                                                    method="delete"
                                                    as="button"
                                                    class="rounded p-1.5 text-red-600 transition hover:bg-red-50 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                    title="Eliminar"
                                                    aria-label="Eliminar"
                                                    preserve-scroll
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Nueva acción</h3>
                <form @submit.prevent="submitCreate" class="mt-4 space-y-4">
                    <div>
                        <InputLabel for="create-name" value="Nombre" />
                        <input
                            id="create-name"
                            v-model="createForm.name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <InputError :message="createForm.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="create-points" value="Puntaje (-10 a 10)" />
                        <select
                            id="create-points"
                            v-model="createForm.points"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option v-for="n in pointsOptions" :key="n" :value="n">
                                {{ n > 0 ? '+' : '' }}{{ n }}
                            </option>
                        </select>
                        <InputError :message="createForm.errors.points" />
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <SecondaryButton type="button" @click="showCreateModal = false">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="createForm.processing">
                            Crear
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Editar acción</h3>
                <form @submit.prevent="submitEdit" class="mt-4 space-y-4">
                    <div>
                        <InputLabel for="edit-name" value="Nombre" />
                        <input
                            id="edit-name"
                            v-model="editForm.name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <InputError :message="editForm.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="edit-points" value="Puntaje (-10 a 10)" />
                        <select
                            id="edit-points"
                            v-model="editForm.points"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option v-for="n in pointsOptions" :key="n" :value="n">
                                {{ n > 0 ? '+' : '' }}{{ n }}
                            </option>
                        </select>
                        <InputError :message="editForm.errors.points" />
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
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
