<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { usePWAInstall } from '@/composables/usePWAInstall';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, default: '' },
    phpVersion: { type: String, default: '' },
    landingImages: {
        type: Object,
        default: () => ({
            dashboard: '/images/landing/mockup-dashboard.png',
            tareas: '/images/landing/mockup-tareas.png',
            recompensas: '/images/landing/mockup-recompensas.png',
        }),
    },
});

const { showInstallBanner, isStandalone, requestInstall } = usePWAInstall();
</script>

<template>
    <Head title="Chanta Puntos - Puntos y recompensas para la familia" />
    <div class="min-h-screen flex flex-col bg-gray-50 text-gray-900">
        <!-- Header -->
        <header class="sticky top-0 z-10 border-b border-gray-200 bg-white/90 backdrop-blur-sm">
            <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-4 sm:px-6">
                <Link href="/" class="flex items-center focus:outline-none">
                    <ApplicationLogo class="h-10 w-auto sm:h-12" />
                </Link>
                <nav v-if="canLogin" class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Ir al dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Iniciar sesión
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-gray-800 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Registrarme
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <!-- Hero -->
            <section class="px-4 py-12 sm:px-6 sm:py-16">
                <div class="mx-auto max-w-4xl text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                        Ayudá a los chicos a sumar en casa
                    </h1>
                    <p class="mt-5 text-xl text-gray-600 sm:text-2xl">
                        Puntos y recompensas para las tareas de cada día. Un juego simple para que entiendan el valor de su aporte en el hogar.
                    </p>
                    <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <button
                            v-if="showInstallBanner"
                            type="button"
                            @click="requestInstall"
                            class="inline-flex w-full max-w-xs items-center justify-center gap-2 rounded-md border border-transparent bg-gray-800 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Instalar app
                        </button>
                        <Link
                            v-if="isStandalone"
                            :href="route('dashboard')"
                            class="inline-flex w-full max-w-xs items-center justify-center rounded-md border border-transparent bg-gray-800 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                        >
                            Abrir Chanta Puntos
                        </Link>
                        <template v-if="canRegister && !showInstallBanner && !isStandalone">
                            <Link
                                :href="route('register')"
                                class="inline-flex w-full max-w-xs items-center justify-center rounded-md border border-transparent bg-gray-800 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                            >
                                Registrarme
                            </Link>
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                class="inline-flex w-full max-w-xs items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                            >
                                Ya tengo cuenta
                            </Link>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Beneficio -->
            <section class="border-t border-gray-200 bg-white px-4 py-12 sm:px-6 sm:py-16">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-xl text-gray-700 sm:text-2xl">
                        Diseñada para papás y mamás: definí tareas, sumá puntos y dejá que canjeen recompensas.
                    </p>
                    <p class="mt-5 text-lg text-gray-600 sm:text-xl">
                        Pequeñas tareas, gran aprendizaje: ordenar, colaborar y ver que su esfuerzo vale.
                    </p>
                </div>
            </section>

            <!-- Cómo funciona -->
            <section class="px-4 py-12 sm:px-6 sm:py-16">
                <div class="mx-auto max-w-4xl">
                    <h2 class="text-center text-3xl font-bold text-gray-900 sm:text-4xl">
                        Cómo funciona
                    </h2>
                    <div class="mt-12 grid gap-8 sm:grid-cols-3 sm:gap-6">
                        <div class="flex flex-col items-center rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/80">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-xl font-bold text-indigo-600">1</span>
                            <h3 class="mt-4 text-xl font-semibold text-gray-900">Definí tareas</h3>
                            <p class="mt-3 text-center text-base text-gray-600 sm:text-lg">
                                Creá acciones con puntos: ordenar el cuarto, poner la mesa, guardar juguetes.
                            </p>
                        </div>
                        <div class="flex flex-col items-center rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/80">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-xl font-bold text-indigo-600">2</span>
                            <h3 class="mt-4 text-xl font-semibold text-gray-900">Sumá puntos</h3>
                            <p class="mt-3 text-center text-base text-gray-600 sm:text-lg">
                                Los chicos completan tareas y suman puntos. Vos validás desde la app.
                            </p>
                        </div>
                        <div class="flex flex-col items-center rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/80">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-xl font-bold text-indigo-600">3</span>
                            <h3 class="mt-4 text-xl font-semibold text-gray-900">Canjeá recompensas</h3>
                            <p class="mt-3 text-center text-base text-gray-600 sm:text-lg">
                                Definí premios y dejá que canjeen con sus puntos cuando lleguen.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Base profesional y método -->
            <section class="border-t border-gray-200 bg-white px-4 py-12 sm:px-6 sm:py-16">
                <div class="mx-auto max-w-3xl text-center">
                    <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                        Desarrollada con respaldo profesional
                    </h2>
                    <p class="mt-6 text-xl text-gray-700 sm:text-2xl">
                        La aplicación está desarrollada bajo la supervisión de psicólogos especializados en adolescencia.
                    </p>
                    <p class="mt-5 text-lg text-gray-600 sm:text-xl">
                        Es un método probado para incentivar actividades proactivas mediante la gamificación, implementada con tecnologías de fácil acceso para toda la familia.
                    </p>
                </div>
            </section>

            <!-- Galería mockups -->
            <section class="border-t border-gray-200 bg-gray-100/50 px-4 py-12 sm:px-6 sm:py-16">
                <div class="mx-auto max-w-md">
                    <h2 class="text-center text-3xl font-bold text-gray-900 sm:text-4xl">
                        Así se ve la app
                    </h2>
                    <div class="mt-12 flex flex-col items-center gap-10">
                        <div class="flex w-full max-w-[220px] flex-col items-center text-center">
                            <div class="w-full overflow-hidden rounded-2xl shadow-lg aspect-[9/19.5] bg-white">
                                <img
                                    :src="landingImages?.dashboard ?? '/images/landing/mockup-dashboard.png'"
                                    alt="Pantalla del dashboard con niños y puntos"
                                    class="h-full w-full object-cover object-center"
                                />
                            </div>
                            <p class="mt-3 text-base text-gray-700 sm:text-lg">
                                Dashboard con los chicos y sus puntos. Sumá o restá puntos según las tareas que cumplan.
                            </p>
                        </div>
                        <div class="flex w-full max-w-[220px] flex-col items-center text-center">
                            <div class="w-full overflow-hidden rounded-2xl shadow-lg aspect-[9/19.5] bg-white">
                                <img
                                    :src="landingImages?.tareas ?? '/images/landing/mockup-tareas.png'"
                                    alt="Lista de tareas con puntos"
                                    class="h-full w-full object-cover object-center"
                                />
                            </div>
                            <p class="mt-3 text-base text-gray-700 sm:text-lg">
                                Definí tareas con sus puntos. Cada acción puede sumar o restar según lo que acuerden en familia.
                            </p>
                        </div>
                        <div class="flex w-full max-w-[220px] flex-col items-center text-center">
                            <div class="w-full overflow-hidden rounded-2xl shadow-lg aspect-[9/19.5] bg-white">
                                <img
                                    :src="landingImages?.recompensas ?? '/images/landing/mockup-recompensas.png'"
                                    alt="Pantalla de recompensas para canjear"
                                    class="h-full w-full object-cover object-center"
                                />
                            </div>
                            <p class="mt-3 text-base text-gray-700 sm:text-lg">
                                Canjeá recompensas. Los chicos usan sus puntos para premios que ustedes elijan.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Copy de conversión -->
            <section class="px-4 py-12 sm:px-6 sm:py-16">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-2xl font-medium text-gray-800 sm:text-3xl">
                        Menos peleas, más complicidad. Instalá la app y probala en familia.
                    </p>
                </div>
            </section>

            <!-- CTA final -->
            <section class="border-t border-gray-200 bg-indigo-600 px-4 py-12 sm:px-6 sm:py-16">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-white sm:text-4xl">
                        Empezá hoy
                    </h2>
                    <p class="mt-4 text-lg text-indigo-100 sm:text-xl">
                        Gratis, sin complicaciones. Creá tu cuenta o instalá la app en tu celular.
                    </p>
                    <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <button
                            v-if="showInstallBanner"
                            type="button"
                            @click="requestInstall"
                            class="inline-flex w-full max-w-xs items-center justify-center gap-2 rounded-md border border-transparent bg-white px-5 py-3 text-sm font-medium text-gray-800 shadow-sm transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600 sm:w-auto"
                        >
                            Instalar app
                        </button>
                        <Link
                            v-if="isStandalone"
                            :href="route('dashboard')"
                            class="inline-flex w-full max-w-xs items-center justify-center rounded-md border border-transparent bg-white px-5 py-3 text-sm font-medium text-gray-800 shadow-sm transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600 sm:w-auto"
                        >
                            Abrir Chanta Puntos
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex w-full max-w-xs items-center justify-center rounded-md border border-transparent bg-white px-5 py-3 text-sm font-medium text-gray-800 shadow-sm transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600 sm:w-auto"
                        >
                            Crear cuenta
                        </Link>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="inline-flex w-full max-w-xs items-center justify-center rounded-md border-2 border-white/60 bg-transparent px-5 py-3 text-sm font-medium text-white transition duration-150 ease-in-out hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600 sm:w-auto"
                        >
                            Ya tengo cuenta
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white px-4 py-8">
            <div class="mx-auto flex max-w-4xl flex-col items-center justify-between gap-4 sm:flex-row">
                <span class="text-base text-gray-500">Chanta Puntos — Puntos y recompensas para la familia</span>
                <nav v-if="canLogin" class="flex gap-6">
                    <Link
                        :href="route('login')"
                        class="text-base font-medium text-gray-600 hover:text-indigo-600"
                    >
                        Iniciar sesión
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="text-base font-medium text-gray-600 hover:text-indigo-600"
                    >
                        Registrarme
                    </Link>
                </nav>
            </div>
        </footer>
    </div>
</template>
