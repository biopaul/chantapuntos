<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Configuración" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-6">
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="9" cy="7" r="2.5"/>
                                <circle cx="16" cy="7" r="2.5"/>
                                <path d="M2 21c0-4 3-7 7-7h6c4 0 7 3 7 7"/>
                            </svg>
                            Configurar hijos
                        </Link>
                        <Link
                            :href="route('actions.index')"
                            class="flex flex-1 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span aria-hidden="true">📋</span>
                            Configurar tareas
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
