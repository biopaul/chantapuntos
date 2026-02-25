<script setup>
/* global __APP_VERSION__ */
import { computed, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const clientVersion = typeof __APP_VERSION__ !== 'undefined' ? String(__APP_VERSION__) : '1.0.0';
const serverVersion = computed(() => page.props.app_version || '1.0.0');

function parseVersion(v) {
    const parts = String(v).trim().split('.').map((n) => parseInt(n, 10) || 0);
    return { major: parts[0] || 0, minor: parts[1] || 0, patch: parts[2] || 0 };
}

function isVersionLess(a, b) {
    const va = parseVersion(a);
    const vb = parseVersion(b);
    if (va.major !== vb.major) return va.major < vb.major;
    if (va.minor !== vb.minor) return va.minor < vb.minor;
    return va.patch < vb.patch;
}

const showModal = ref(false);

onMounted(() => {
    if (isVersionLess(clientVersion, serverVersion)) {
        showModal.value = true;
    }
});

function updateNow() {
    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
        navigator.serviceWorker.getRegistration().then((reg) => {
            if (reg && reg.waiting) reg.waiting.postMessage({ type: 'SKIP_WAITING' });
        });
    }
    window.location.reload();
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="update-title"
            >
                <div
                    class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl"
                    @click.stop
                >
                    <h2
                        id="update-title"
                        class="text-lg font-semibold text-gray-900"
                    >
                        Actualización requerida
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Hay una nueva versión de Chanta Puntos. Actualizá la app para seguir usando.
                    </p>
                    <div class="mt-6">
                        <button
                            type="button"
                            class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            @click="updateNow"
                        >
                            Actualizar ahora
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
