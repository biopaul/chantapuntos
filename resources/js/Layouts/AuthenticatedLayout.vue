<script setup>
import UpdateRequiredModal from '@/Components/UpdateRequiredModal.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

function isActive(...names) {
    const current = route().current();
    return names.some((name) => current === name || (typeof name === 'string' && current?.startsWith(name)));
}
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <!-- Cabecera fija: usuario + Cerrar sesión -->
            <header class="fixed left-0 right-0 top-0 z-50 border-b border-gray-200 bg-white shadow-sm">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2 px-4 py-2 sm:px-6 lg:px-8">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-base font-semibold leading-tight text-gray-800">
                            {{ page.props.auth?.user?.name ?? '' }}
                        </p>
                        <p class="truncate text-xs font-medium text-gray-500">
                            {{ page.props.auth?.user?.email ?? '' }}
                        </p>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="shrink-0 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Cerrar sesión
                    </Link>
                </div>
            </header>

            <!-- Espacio para la cabecera fija -->
            <div class="pt-14">
                <!-- Flash message -->
                <div
                    v-if="page.props.flash?.message"
                    class="bg-indigo-600 px-4 py-2 text-center text-sm text-white"
                >
                    {{ page.props.flash.message }}
                </div>

                <!-- Page Heading (título específico de la página, opcional) -->
                <header
                    v-if="$slots.header"
                    class="bg-white shadow-sm"
                >
                    <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="pb-20">
                    <slot />
                </main>
            </div>

            <!-- Bottom navigation -->
            <nav
                class="fixed bottom-0 left-0 right-0 z-40 rounded-t-2xl border-t border-gray-200 bg-white shadow-lg"
                aria-label="Navegación principal"
            >
                <div class="mx-auto flex h-16 max-w-lg items-center justify-around">
                    <Link
                        :href="route('dashboard')"
                        class="flex flex-col items-center justify-center gap-0.5 py-2 px-3 transition"
                        :class="isActive('dashboard') ? 'text-indigo-600' : 'text-gray-500'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11v2a1 1 0 01-1 1h2m-6-1a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6-1h6" />
                        </svg>
                        <span class="text-xs font-medium">Inicio</span>
                    </Link>
                    <Link
                        :href="route('history.index')"
                        class="flex flex-col items-center justify-center gap-0.5 py-2 px-3 transition"
                        :class="isActive('history.index') ? 'text-indigo-600' : 'text-gray-500'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-medium">Historial</span>
                    </Link>
                    <Link
                        :href="route('redemptions.create')"
                        class="flex flex-col items-center justify-center gap-0.5 py-2 px-3 transition"
                        :class="isActive('redemptions.create') ? 'text-indigo-600' : 'text-gray-500'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 003 3h-15a3 3 0 003-3m-9 0V3.375c0-.621.504-1.125 1.125-1.125h.871M7.5 18.75V3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                        <span class="text-xs font-medium">Canjes</span>
                    </Link>
                    <Link
                        :href="route('profile.edit')"
                        class="flex flex-col items-center justify-center gap-0.5 py-2 px-3 transition"
                        :class="isActive('profile.edit') ? 'text-indigo-600' : 'text-gray-500'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-xs font-medium">Perfil</span>
                    </Link>
                </div>
            </nav>
        </div>
        <UpdateRequiredModal />
    </div>
</template>
