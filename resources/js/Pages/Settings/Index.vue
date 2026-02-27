<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Configuración" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h2 class="text-lg font-medium text-gray-900">
                        Configuración
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Administrá hijos, tareas y otras opciones de la app.
                    </p>
                    <div class="mt-6 flex max-w-xl flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <Link
                            :href="route('onboarding')"
                            class="flex flex-1 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span aria-hidden="true">👶</span>
                            Hijos
                        </Link>
                        <Link
                            :href="route('actions.index')"
                            class="flex flex-1 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span aria-hidden="true">📋</span>
                            Tareas
                        </Link>
                        <Link
                            :href="route('profile.edit')"
                            class="flex flex-1 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span aria-hidden="true">👤</span>
                            Perfil y eliminación de cuenta
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
