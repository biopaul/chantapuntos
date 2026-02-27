<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Skeleton from '@/Components/Skeleton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    children: { type: Array, required: true },
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
    child_id: '',
    description: '',
    points: 1,
});

const selectedChild = computed(() =>
    props.children.find((c) => String(c.id) === String(form.child_id))
);

const maxPoints = computed(() => selectedChild.value?.points ?? 0);

const hasChildrenWithPoints = computed(() => props.children.length > 0);
</script>

<template>
    <Head title="Canje de puntos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Canje de puntos
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <template v-if="!showContent">
                            <div class="mb-6 flex justify-center" aria-hidden="true">
                                <Skeleton variant="circle" class="h-16 w-16" />
                            </div>
                            <div class="mb-6 space-y-2">
                                <Skeleton variant="line" class="w-full" />
                                <Skeleton variant="line" class="w-11/12" />
                            </div>
                            <div class="space-y-4">
                                <Skeleton variant="line" class="h-10 w-full" />
                                <Skeleton variant="line" class="h-10 w-full" />
                                <Skeleton variant="line" class="h-10 w-32" />
                            </div>
                        </template>
                        <template v-else>
                        <div class="mb-6 flex justify-center" aria-hidden="true">
                            <span class="text-7xl">💰</span>
                        </div>
                        <p class="mb-6 text-gray-600">
                            Elige el hijo, describe el canje (ej. "Llevar a Isabella al cumpleaños de Juanita") y los puntos a restar.
                        </p>
                        <p
                            v-if="!hasChildrenWithPoints"
                            class="rounded-lg bg-amber-50 p-4 text-amber-800"
                        >
                            Ningún hijo tiene puntos para canjear. Solo se puede canjear cuando el balance es positivo.
                        </p>
                        <form
                            v-else
                            @submit.prevent="form.post(route('points.redeem'), { preserveScroll: true })"
                            class="space-y-6"
                        >
                            <div>
                                <InputLabel for="child_id" value="Hijo" />
                                <select
                                    id="child_id"
                                    v-model="form.child_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Selecciona...</option>
                                    <option
                                        v-for="c in children"
                                        :key="c.id"
                                        :value="c.id"
                                    >
                                        {{ c.name }} ({{ c.points }} pts)
                                    </option>
                                </select>
                                <InputError :message="form.errors.child_id" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Descripción del canje" />
                                <input
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                    maxlength="255"
                                    placeholder="Ej. Llevar a Isabella al cumpleaños de Juanita"
                                />
                                <InputError :message="form.errors.description" />
                            </div>

                            <div>
                                <InputLabel for="points" value="Puntos a canjear" />
                                <input
                                    id="points"
                                    v-model.number="form.points"
                                    type="number"
                                    min="1"
                                    :max="maxPoints"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <p v-if="selectedChild" class="mt-1 text-sm text-gray-500">
                                    Máximo: {{ selectedChild.points }} pts
                                </p>
                                <InputError :message="form.errors.points" />
                            </div>

                            <div class="flex gap-3">
                                <PrimaryButton type="submit" :disabled="form.processing">
                                    Registrar canje
                                </PrimaryButton>
                                <Link
                                    :href="route('dashboard')"
                                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
