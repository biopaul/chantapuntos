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
            <!-- Cabecera fija: usuario + Cerrar sesión (sin borde para unificar con barra de estado) -->
            <header class="fixed left-0 right-0 top-0 z-50 bg-[#e8e8e8]">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2 px-4 py-2 sm:px-6 lg:px-8">
                    <Link
                        :href="route('profile.edit')"
                        class="flex min-w-0 flex-1 items-center gap-2 rounded-md py-1 pr-2 transition hover:bg-gray-200/80 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-[#e8e8e8]"
                        title="Ir al perfil"
                    >
                        <span class="shrink-0 text-gray-600" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1 truncate">
                            <p class="truncate text-base font-semibold leading-tight text-gray-800">
                                {{ page.props.auth?.user?.name ?? '' }}
                            </p>
                            <p class="truncate text-xs font-medium text-gray-500">
                                {{ page.props.auth?.user?.email ?? '' }}
                            </p>
                        </div>
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="shrink-0 rounded-md p-2 text-gray-800 transition hover:bg-gray-200/80 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-[#e8e8e8]"
                        title="Cerrar sesión"
                        aria-label="Cerrar sesión"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
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
                        <!-- Casa simple: techo + cuerpo -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 4L4 12h2v8h4v-5h4v5h4v-8h2L12 4z" />
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
                        <!-- Bolsa (canjes / compras) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="text-xs font-medium">Canjes</span>
                    </Link>
                    <Link
                        :href="route('settings.index')"
                        class="flex flex-col items-center justify-center gap-0.5 py-2 px-3 transition"
                        :class="isActive('settings.index') ? 'text-indigo-600' : 'text-gray-500'"
                    >
                        <!-- Engranaje / Configuración -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-xs font-medium">Configuración</span>
                    </Link>
                </div>
            </nav>
        </div>
        <UpdateRequiredModal />
    </div>
</template>
