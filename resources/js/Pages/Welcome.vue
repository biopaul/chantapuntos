<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { usePWAInstall } from '@/composables/usePWAInstall';
import { Head, Link } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';

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
            mascota: '/images/landing/mascota-llama.png',
        }),
    },
});

const {
    showInstallBanner,
    showFallbackHint,
    fallbackHintText,
    isStandalone,
    requestInstall,
    dismissFallbackHint,
} = usePWAInstall();

// Testimonios: carrusel
const testimonials = [
    { name: 'Laura G.', location: 'Buenos Aires', quote: 'Mis hijos de 12 y 14 finalmente entienden que el tiempo de PlayStation no es un derecho divino, sino un depósito emocional que tienen que ganar.' },
    { name: 'Diego M.', location: 'Córdoba', quote: 'Se terminaron los gritos para que se duchen. Ahora miran la app, ven que están en \'rojo\' y van solos. Es magia o psicología, no sé, pero funciona.' },
    { name: 'Marta S.', location: 'Rosario', quote: 'Lo mejor es la transparencia. Ya no hay \'te juro que hice la cama\'. Si no hay validación en la app, no hay Chantapuntos. ¡Paz total!' },
    { name: 'Santi', location: 'Hijo, 11 años', quote: 'Al principio me parecía un bajón, pero ahora sé que si ayudo con el perro el finde tengo permiso para jugar más tiempo. Es un trato justo.' },
    { name: 'Elena R.', location: 'Mendoza', quote: 'Siguiendo el consejo de nuestra psicóloga, la app nos ayudó a poner límites sin ser los \'malos\'. Las reglas están claras para todos.' },
    { name: 'Ricardo F.', location: 'Salta', quote: 'Es genial para adolescentes. A esa edad todo es negociación, y Chantapuntos es el mercado donde cerramos los mejores tratos.' },
    { name: 'Julia V.', location: 'Montevideo', quote: 'El concepto de la Cuenta Corriente Emocional cambió la dinámica. Los chicos ven sus progresos y nosotros valoramos su esfuerzo real.' },
    { name: 'Felipe T.', location: 'La Plata', quote: '¡Por fin una app que no es para nenes chiquitos! El diseño de la llama y el estilo irreverente conectó de una con mis hijos.' },
    { name: 'Valeria N.', location: 'Neuquén', quote: 'Logramos que el cuarto esté ordenado tres días seguidos. Es un récord histórico en esta casa gracias al sistema de puntos.' },
    { name: 'Tomás L.', location: 'Tucumán', quote: 'Pasamos de la pelea constante al acuerdo mutuo. Es una herramienta de convivencia que realmente necesitábamos.' },
];

const testimonialCarousel = ref(null);
const testimonialPage = ref(0);
const testimonialTotalPages = ref(4);
let testimonialAutoplay = null;

function getTestimonialVisibleCount() {
    if (typeof window === 'undefined') return 3;
    const w = window.innerWidth;
    if (w < 768) return 1;
    if (w < 1024) return 2;
    return 3;
}

function updateTestimonialTotalPages() {
    const n = getTestimonialVisibleCount();
    testimonialTotalPages.value = Math.ceil(testimonials.length / n);
}

function goToTestimonialPage(index) {
    if (!testimonialCarousel.value) return;
    updateTestimonialTotalPages();
    const total = testimonialTotalPages.value;
    const i = Math.max(0, Math.min(index, total - 1));
    testimonialPage.value = i;
    const slideWidth = testimonialCarousel.value.scrollWidth / testimonials.length;
    const visible = getTestimonialVisibleCount();
    testimonialCarousel.value.scrollTo({
        left: i * visible * slideWidth,
        behavior: 'smooth',
    });
}

function nextTestimonialPage() {
    updateTestimonialTotalPages();
    const next = testimonialPage.value + 1;
    if (next >= testimonialTotalPages.value) goToTestimonialPage(0);
    else goToTestimonialPage(next);
}

function onTestimonialScroll() {
    if (!testimonialCarousel.value) return;
    const visible = getTestimonialVisibleCount();
    const slideWidth = testimonialCarousel.value.scrollWidth / testimonials.length;
    const page = Math.round(testimonialCarousel.value.scrollLeft / (visible * slideWidth));
    testimonialPage.value = Math.min(page, testimonialTotalPages.value - 1);
}

onMounted(() => {
    updateTestimonialTotalPages();
    testimonialAutoplay = setInterval(nextTestimonialPage, 11000);
    window.addEventListener('resize', updateTestimonialTotalPages);
});

onBeforeUnmount(() => {
    if (testimonialAutoplay) clearInterval(testimonialAutoplay);
    window.removeEventListener('resize', updateTestimonialTotalPages);
});

// FAQ: acordeón (solo uno abierto a la vez)
const faqItems = [
    { q: '¿Qué es exactamente un "Chantapunto"?', a: 'Es la unidad de medida de nuestro sistema. Un Chantapunto representa un "depósito" en la cuenta corriente emocional de tu hijo. Se ganan cumpliendo responsabilidades y se utilizan para acceder a recompensas o permisos acordados.' },
    { q: '¿A partir de qué edad se recomienda usar la app?', a: 'Aunque funciona con niños de todas las edades, está optimizada para la etapa de los 11 a 14 años, donde la negociación y la autonomía son claves en el desarrollo.' },
    { q: '¿Cómo ayuda la app a reducir las discusiones en casa?', a: 'La app aporta transparencia. Al tener las tareas y sus valores definidos de antemano, se elimina la subjetividad. Ya no es "porque yo lo digo", sino "porque es el trato que figura en tu cuenta".' },
    { q: '¿Qué pasa si mi hijo se olvida de una tarea?', a: 'El sistema permite realizar "retiros" de la cuenta corriente emocional. No se trata de un castigo, sino de una consecuencia lógica: si no hay depósito de esfuerzo, el saldo baja.' },
    { q: '¿La app reemplaza el diálogo con mis hijos?', a: 'Al contrario, es una herramienta para fomentarlo. Chantapuntos es el soporte técnico para que las negociaciones y los pactos familiares tengan un seguimiento justo y divertido.' },
    { q: '¿Los premios tienen que ser materiales?', a: 'Para nada. De hecho, recomendamos premios de "tiempo" o "experiencias": media hora extra de consola, elegir la cena del viernes o un permiso para volver más tarde.' },
    { q: '¿Qué dice la psicología sobre este método?', a: 'Se basa en el Refuerzo Positivo y la Economía de Fichas, técnicas validadas para fomentar hábitos. Además, integra el concepto de Stephen Covey sobre los depósitos de confianza en las relaciones.' },
    { q: '¿Es difícil configurar las tareas iniciales?', a: 'No, la app viene con plantillas sugeridas por profesionales que podés activar con un toque y luego personalizar según la dinámica de tu hogar.' },
];
const openFaqIndex = ref(null);

function toggleFaq(index) {
    openFaqIndex.value = openFaqIndex.value === index ? null : index;
}
</script>

<template>
    <Head title="Chanta Puntos - Puntos y recompensas para la familia" />
    <div class="min-h-screen flex flex-col bg-gray-50 text-gray-900">
        <!-- Header -->
        <header class="sticky top-0 z-10 w-full border-b border-gray-200 bg-white/90 backdrop-blur-sm">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="flex justify-between items-center py-4 md:py-6">
                    <Link href="/" class="flex items-center focus:outline-none">
                        <ApplicationLogo class="h-10 w-auto sm:h-12 lg:h-14" />
                    </Link>
                    <nav v-if="canLogin" class="flex items-center gap-3">
                        <Link
                            v-if="$page.props.auth?.user"
                            :href="route('dashboard')"
                            class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2"
                        >
                            Ir al dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2"
                            >
                                Iniciar sesión
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="inline-flex items-center justify-center rounded-md border border-transparent bg-gray-800 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2"
                            >
                                Registrarme
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <!-- Hero (split layout, SaaS-style) -->
            <section class="relative overflow-hidden bg-gradient-to-br from-[#FFFBEB] via-white to-gray-50/80 px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
                <div class="mx-auto max-w-7xl">
                    <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-14">
                        <!-- Left: copy + CTAs -->
                        <div class="flex flex-col justify-center lg:order-1">
                            <Transition
                                enter-active-class="transition duration-500 ease-out"
                                enter-from-class="opacity-0 translate-y-4"
                                enter-to-class="opacity-100 translate-y-0"
                                appear
                            >
                                <div class="space-y-6">
                                    <span
                                        class="inline-block rounded-full bg-amber-100 px-4 py-1.5 text-sm font-medium text-amber-800 ring-1 ring-amber-200/60"
                                    >
                                        Psicología Positiva aplicada
                                    </span>
                                    <h1 class="text-[#1F2937] text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl" style="letter-spacing: -0.02em;">
                                        De caprichos a responsabilidades: Convertí las tareas del hogar en un
                                        <span class="relative inline-block">
                                            <span class="text-amber-500">trato</span>
                                            <!-- Subrayado hand-drawn: trazo orgánico tipo acuerdo humano, no rígido -->
                                            <svg class="absolute -bottom-0.5 left-0 w-full" viewBox="0 0 100 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M2 6.5 Q22 8 40 5.2 Q58 7 75 5.8 Q90 6.5 98 6"
                                                    stroke="#FBBF24"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    fill="none"
                                                />
                                            </svg>
                                        </span>
                                        .
                                    </h1>
                                    <p class="max-w-xl text-lg text-[#1F2937]/90 sm:text-xl mb-2">
                                        Chantapuntos es la herramienta de parenting que ayuda a gestionar el "mercado" de las tareas diarias. Basada en el concepto de Cuenta Corriente Emocional, transformamos la convivencia en un sistema claro, justo y divertido.
                                    </p>
                                    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap mt-8">
                                        <template v-if="showInstallBanner">
                                            <button
                                                type="button"
                                                @click="requestInstall"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-3xl bg-amber-400 px-6 py-3.5 text-base font-semibold text-[#1F2937] shadow-lg shadow-amber-400/25 transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 sm:w-auto"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Empezar ahora
                                            </button>
                                        </template>
                                        <Link
                                            v-else-if="isStandalone"
                                            :href="route('dashboard')"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-3xl bg-amber-400 px-6 py-3.5 text-base font-semibold text-[#1F2937] shadow-lg shadow-amber-400/25 transition hover:bg-amber-500 sm:w-auto"
                                        >
                                            Empezar ahora
                                        </Link>
                                        <Link
                                            v-else-if="canRegister"
                                            :href="route('register')"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-3xl bg-amber-400 px-6 py-3.5 text-base font-semibold text-[#1F2937] shadow-lg shadow-amber-400/25 transition hover:bg-amber-500 sm:w-auto"
                                        >
                                            Empezar ahora
                                        </Link>
                                        <a
                                            href="#como-funciona"
                                            class="inline-flex w-full items-center justify-center rounded-3xl border border-gray-300 bg-white/80 px-6 py-3.5 text-base font-medium text-[#1F2937] transition hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:ring-offset-2 sm:w-auto"
                                        >
                                            Ver cómo funciona
                                        </a>
                                    </div>
                                    <p v-if="showFallbackHint" class="flex flex-wrap items-center gap-2 rounded-2xl bg-amber-50 px-4 py-2.5 text-sm text-amber-800 ring-1 ring-amber-200/60">
                                        <span>{{ fallbackHintText }}</span>
                                        <button type="button" aria-label="Cerrar" class="shrink-0 rounded-lg p-1 hover:bg-amber-100" @click="dismissFallbackHint">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Recomendado por psicólogos infantiles y familias modernas.
                                    </p>
                                    <p v-if="canLogin && !$page.props.auth?.user" class="text-sm text-gray-500">
                                        ¿Ya tenés cuenta?
                                        <Link :href="route('login')" class="font-medium text-amber-600 underline decoration-amber-400/60 underline-offset-2 hover:text-amber-700">
                                            Iniciar sesión
                                        </Link>
                                    </p>
                                </div>
                            </Transition>
                        </div>
                        <!-- Right: mascot / illustration placeholder -->
                        <div class="flex justify-center lg:order-2">
                            <Transition
                                enter-active-class="transition duration-600 ease-out delay-150"
                                enter-from-class="opacity-0 translate-x-4"
                                enter-to-class="opacity-100 translate-x-0"
                                appear
                            >
                                <div class="w-full max-w-md rounded-3xl bg-white/60 p-6 shadow-xl ring-1 ring-gray-200/60 backdrop-blur-sm sm:p-8 lg:max-w-lg">
                                    <!-- Placeholder: reemplazá por tu imagen de mascota (llama) -->
                                    <div class="flex aspect-square items-center justify-center rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/80 text-gray-400">
                                        <div class="text-center">
                                            <svg class="mx-auto h-16 w-16 text-amber-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6a2 2 0 11-4 0 2 2 0 014 0zM7 20h10" />
                                            </svg>
                                            <p class="mt-2 text-sm font-medium">Tu mascota (llama) aquí</p>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>
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
            <section id="como-funciona" class="scroll-mt-20 px-4 py-12 sm:px-6 sm:py-16">
                <div class="mx-auto max-w-6xl">
                    <h2 class="text-center text-3xl font-bold text-gray-900 sm:text-4xl">
                        Cómo funciona
                    </h2>
                    <!-- Grid: 1 col mobile, 3 cols + conectores punteados en desktop -->
                    <div class="mt-12 flex flex-col items-stretch gap-8 lg:grid lg:grid-cols-[1fr_auto_1fr_auto_1fr] lg:items-start lg:gap-x-4">
                        <!-- Paso 1 -->
                        <div
                            class="group flex flex-col rounded-3xl border border-gray-200/90 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-amber-400 hover:shadow-md sm:p-8"
                        >
                            <div class="flex justify-center">
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </span>
                            </div>
                            <h3 class="mt-5 text-center text-xl font-semibold text-gray-900">
                                Definí las reglas del juego
                            </h3>
                            <p class="mt-3 text-center text-base leading-relaxed text-gray-600">
                                Creá tareas personalizadas según la edad de tus hijos. Desde mantener el cuarto ordenado hasta cumplir el horario de estudio. Vos definís cuánto vale cada esfuerzo.
                            </p>
                        </div>

                        <!-- Conector punteado (solo desktop) -->
                        <div class="hidden shrink-0 self-center lg:block" aria-hidden="true">
                            <div class="h-0.5 w-8 border-t-2 border-dashed border-amber-300/80 lg:w-12"></div>
                        </div>

                        <!-- Paso 2 -->
                        <div
                            class="group flex flex-col rounded-3xl border border-gray-200/90 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-amber-400 hover:shadow-md sm:p-8"
                        >
                            <div class="flex justify-center">
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                            </div>
                            <h3 class="mt-5 text-center text-xl font-semibold text-gray-900">
                                Gestioná los depósitos emocionales
                            </h3>
                            <p class="mt-3 text-center text-base leading-relaxed text-gray-600">
                                Asigná o restá Chantapuntos en tiempo real. La transparencia total elimina las discusiones: los puntos están a la vista de todos.
                            </p>
                        </div>

                        <!-- Conector punteado (solo desktop) -->
                        <div class="hidden shrink-0 self-center lg:block" aria-hidden="true">
                            <div class="h-0.5 w-8 border-t-2 border-dashed border-amber-300/80 lg:w-12"></div>
                        </div>

                        <!-- Paso 3 -->
                        <div
                            class="group flex flex-col rounded-3xl border border-gray-200/90 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-amber-400 hover:shadow-md sm:p-8"
                        >
                            <div class="flex justify-center">
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                    </svg>
                                </span>
                            </div>
                            <h3 class="mt-5 text-center text-xl font-semibold text-gray-900">
                                Canjeá por recompensas reales
                            </h3>
                            <p class="mt-3 text-center text-base leading-relaxed text-gray-600">
                                Los puntos acumulados se transforman en premios acordados: tiempo de pantalla, salidas con amigos o ese permiso especial que tanto buscaban.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Respaldo profesional (dark block) -->
            <section class="relative overflow-hidden bg-slate-950 px-4 py-16 sm:px-6 sm:py-20 lg:py-24">
                <!-- Gráfico lineal tenue de fondo (onda crecimiento/conexión) -->
                <div class="pointer-events-none absolute inset-0 opacity-[0.07]" aria-hidden="true">
                    <svg class="h-full w-full" viewBox="0 0 1200 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 280 Q150 200 300 240 T600 200 T900 260 T1200 180" stroke="#FBBF24" stroke-width="1.5" fill="none" />
                        <path d="M0 320 Q200 260 400 300 T800 240 T1200 300" stroke="#FBBF24" stroke-width="1" fill="none" opacity="0.7" />
                        <path d="M0 360 Q250 300 500 340 T1000 280 L1200 320" stroke="#FBBF24" stroke-width="0.8" fill="none" opacity="0.5" />
                    </svg>
                </div>

                <div class="relative mx-auto max-w-6xl">
                    <div class="grid items-start gap-12 lg:grid-cols-2 lg:gap-16">
                        <!-- Izquierda: badge, título, texto -->
                        <div class="flex flex-col">
                            <span class="inline-block w-fit rounded-full bg-amber-400/15 px-4 py-1.5 text-sm font-medium text-amber-400 ring-1 ring-amber-400/30">
                                Respaldo profesional
                            </span>
                            <h2 class="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl">
                                Desarrollada con respaldo profesional
                            </h2>
                            <p class="mt-6 text-lg leading-relaxed text-slate-200 sm:text-xl">
                                La aplicación está desarrollada bajo la supervisión de psicólogos especializados en adolescencia. Es un método probado para incentivar actividades proactivas mediante la gamificación, implementada con tecnologías de fácil acceso para toda la familia.
                            </p>
                            <p class="mt-5 text-base leading-relaxed text-slate-300 sm:text-lg">
                                El marco teórico se apoya en el concepto de
                                <span class="font-semibold text-amber-400">Cuenta Corriente Emocional</span>
                                : un sistema claro y previsible que reduce conflictos y refuerza el vínculo entre padres e hijos.
                            </p>
                        </div>

                        <!-- Derecha: 3 puntos clave -->
                        <div class="flex flex-col gap-6 rounded-2xl border border-slate-700/80 bg-slate-900/50 p-6 backdrop-blur-sm sm:p-8">
                            <div class="flex gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-400/10 text-amber-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Validación</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">
                                        Basado en evidencia y supervisado por profesionales de la salud mental infantil y adolescente.
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-400/10 text-amber-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Inteligencia emocional</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">
                                        Promueve el reconocimiento del esfuerzo y el diálogo, no solo el resultado.
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-400/10 text-amber-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Enfoque no punitivo</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">
                                        Los puntos premian el avance; no se trata de castigar, sino de hacer visible el aporte de cada uno.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial / cita -->
                    <div class="mt-14 flex justify-center sm:mt-16">
                        <blockquote class="max-w-2xl rounded-2xl border border-slate-700/60 bg-slate-800/40 px-6 py-5 sm:px-8 sm:py-6">
                            <p class="text-center font-serif text-lg italic leading-relaxed text-slate-200 sm:text-xl">
                                "Chantapuntos traduce conceptos de psicología positiva en una herramienta que las familias pueden usar cada día. El acuerdo claro reduce la ansiedad de todos."
                            </p>
                            <footer class="mt-4 text-center text-sm text-slate-400">
                                — Supervisión en diseño de producto, equipo de psicología
                            </footer>
                        </blockquote>
                    </div>
                </div>
            </section>

            <!-- Testimonios -->
            <section class="bg-slate-50 px-4 py-16 sm:px-6 sm:py-20">
                <div class="mx-auto max-w-6xl">
                    <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Lo que dicen las familias que dejaron de lidiar con "chantas"
                    </h2>

                    <div class="relative mt-12">
                        <!-- Flechas (opcionales) -->
                        <button
                            type="button"
                            aria-label="Testimonio anterior"
                            class="absolute left-0 top-1/2 z-10 -translate-y-1/2 rounded-full border border-gray-200 bg-white p-2.5 text-gray-600 shadow-md transition hover:border-amber-300 hover:bg-white hover:text-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400/50 md:-left-4 lg:-left-6"
                            @click="goToTestimonialPage(testimonialPage - 1)"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            aria-label="Siguiente testimonio"
                            class="absolute right-0 top-1/2 z-10 -translate-y-1/2 rounded-full border border-gray-200 bg-white p-2.5 text-gray-600 shadow-md transition hover:border-amber-300 hover:bg-white hover:text-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400/50 md:-right-4 lg:-right-6"
                            @click="nextTestimonialPage"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Carrusel (wrapper recorta costados para efecto continuidad) -->
                        <div class="overflow-hidden -mx-3 sm:-mx-4 md:-mx-5 lg:-mx-6">
                            <div
                                ref="testimonialCarousel"
                                class="flex snap-x snap-mandatory gap-6 overflow-x-auto overflow-y-hidden pb-4 scroll-smooth px-3 sm:px-4 md:px-5 lg:px-6 md:gap-8 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                                @scroll="onTestimonialScroll"
                            >
                                <div
                                    v-for="(t, i) in testimonials"
                                    :key="i"
                                    class="min-w-[88%] shrink-0 snap-center md:min-w-[calc(50%+0.5rem)] lg:min-w-[calc(33.333%+0.5rem)]"
                                >
                                <article class="flex h-full flex-col rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                                    <!-- 5 estrellas -->
                                    <div class="flex gap-0.5 text-amber-400" aria-hidden="true">
                                        <svg v-for="star in 5" :key="star" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                    <p class="mt-4 flex-1 text-base leading-relaxed text-gray-700">
                                        "{{ t.quote }}"
                                    </p>
                                    <div class="mt-5 flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="flex flex-wrap items-center gap-2 font-semibold text-gray-900">
                                                {{ t.name }}
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200/80">
                                                    <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                                    </svg>
                                                    Familia verificada
                                                </span>
                                            </p>
                                            <p class="text-sm text-gray-500">{{ t.location }}</p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-8 flex justify-center gap-2">
                            <button
                                v-for="p in testimonialTotalPages"
                                :key="p"
                                type="button"
                                :aria-label="`Ir a testimonio ${p}`"
                                :class="[
                                    'h-2 rounded-full transition-all',
                                    p - 1 === testimonialPage
                                        ? 'w-8 bg-amber-400'
                                        : 'w-2 bg-gray-300 hover:bg-gray-400',
                                ]"
                                @click="goToTestimonialPage(p - 1)"
                            />
                        </div>
                    </div>
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

            <!-- FAQ -->
            <section class="border-t border-gray-200 bg-white px-4 py-16 sm:px-6 sm:py-20">
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Preguntas frecuentes sobre el método
                    </h2>
                    <div class="mt-12">
                        <div
                            v-for="(item, index) in faqItems"
                            :key="index"
                            class="border-b border-gray-200/80 last:border-b-0"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 py-5 text-left transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/50 focus-visible:ring-offset-2"
                                :class="openFaqIndex === index ? 'text-amber-600' : 'text-gray-900'"
                                @click="toggleFaq(index)"
                            >
                                <span class="font-semibold" :class="openFaqIndex === index ? 'text-amber-600' : 'text-gray-900'">
                                    {{ item.q }}
                                </span>
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 transition-all duration-200 ease-in-out"
                                    :class="openFaqIndex === index ? 'rotate-180 bg-amber-100 text-amber-600' : 'text-gray-600'"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </button>
                            <Transition
                                enter-active-class="transition-all duration-200 ease-in-out"
                                enter-from-class="max-h-0 opacity-0"
                                enter-to-class="max-h-[500px] opacity-100"
                                leave-active-class="transition-all duration-200 ease-in-out"
                                leave-from-class="max-h-[500px] opacity-100"
                                leave-to-class="max-h-0 opacity-0"
                            >
                                <div v-show="openFaqIndex === index" class="overflow-hidden">
                                    <div class="pb-6 pl-0 pr-0 pt-1 sm:pb-8 sm:pl-1 sm:pr-2">
                                        <p class="text-base leading-relaxed text-gray-600 sm:text-lg">
                                            {{ item.a }}
                                        </p>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA final (split asimétrico, cohesión con Hero) -->
            <section class="relative overflow-hidden border-t border-gray-200 bg-gradient-to-br from-[#FFFBEB] via-slate-50 to-white px-4 py-16 sm:px-6 sm:py-20 lg:py-24">
                <div class="relative mx-auto max-w-6xl">
                    <div class="grid items-center gap-10 lg:grid-cols-[1fr_0.65fr] lg:gap-14">
                        <!-- Izquierda (~60%): texto y botones -->
                        <div class="flex flex-col justify-center lg:max-w-xl">
                            <h2 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl" style="letter-spacing: -0.02em;">
                                Menos dramas, más
                                <span class="relative inline-block">
                                    <span class="text-amber-500">complicidad</span>
                                    <svg class="absolute -bottom-0.5 left-0 w-full" viewBox="0 0 100 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 6.5 Q22 8 40 5.2 Q58 7 75 5.8 Q90 6.5 98 6" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                    </svg>
                                </span>
                                . Sumá
                                <span class="text-amber-500">Chantapuntos</span>
                                y transformá tu hogar.
                            </h2>
                            <p class="mt-6 text-lg leading-relaxed text-slate-700 sm:text-xl">
                                Creá tu cuenta gratis y probalo en familia. Instalá la app en tu celular y empezá a sumar depósitos emocionales hoy.
                            </p>
                            <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:flex-wrap">
                                <template v-if="showInstallBanner">
                                    <button
                                        type="button"
                                        @click="requestInstall"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-3xl bg-amber-400 px-6 py-3.5 text-base font-semibold text-slate-900 shadow-lg shadow-amber-400/25 transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 sm:w-auto"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Crear cuenta gratis
                                    </button>
                                </template>
                                <Link
                                    v-else-if="isStandalone"
                                    :href="route('dashboard')"
                                    class="inline-flex w-full items-center justify-center rounded-3xl bg-amber-400 px-6 py-3.5 text-base font-semibold text-slate-900 shadow-lg shadow-amber-400/25 transition hover:bg-amber-500 sm:w-auto"
                                >
                                    Abrir Chanta Puntos
                                </Link>
                                <Link
                                    v-else-if="canRegister"
                                    :href="route('register')"
                                    class="inline-flex w-full items-center justify-center rounded-3xl bg-amber-400 px-6 py-3.5 text-base font-semibold text-slate-900 shadow-lg shadow-amber-400/25 transition hover:bg-amber-500 sm:w-auto"
                                >
                                    Crear cuenta gratis
                                </Link>
                                <Link
                                    v-if="canLogin"
                                    :href="route('login')"
                                    class="inline-flex w-full items-center justify-center rounded-3xl border-2 border-amber-400 bg-white px-6 py-3.5 text-base font-semibold text-amber-600 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:ring-offset-2 sm:w-auto"
                                >
                                    Ya tengo cuenta
                                </Link>
                            </div>
                            <p v-if="showFallbackHint" class="mt-4 flex flex-wrap items-center gap-2 rounded-2xl bg-amber-50 px-4 py-2.5 text-sm text-amber-800 ring-1 ring-amber-200/60">
                                <span>{{ fallbackHintText }}</span>
                                <button type="button" aria-label="Cerrar" class="shrink-0 rounded-lg p-1 hover:bg-amber-100" @click="dismissFallbackHint">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </p>
                            <p class="mt-6 text-sm text-slate-500">
                                Recomendado por psicólogos infantiles y familias modernas.
                            </p>
                        </div>
                        <!-- Derecha (~40%): mascota -->
                        <div class="flex justify-center lg:justify-end">
                            <div class="relative w-full max-w-[280px] sm:max-w-[320px] lg:max-w-[380px]">
                                <img
                                    :src="landingImages?.mascota ?? '/images/landing/mascota-llama.png'"
                                    alt="Mascota Chantapuntos"
                                    class="h-auto w-full object-contain object-right"
                                />
                            </div>
                        </div>
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
