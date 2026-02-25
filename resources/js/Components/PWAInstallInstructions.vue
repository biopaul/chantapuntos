<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { computed } from 'vue';

defineProps({
    show: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const isIOS = computed(() => {
    if (typeof navigator === 'undefined') return false;
    return /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
});
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">
                Cómo instalar Chanta Puntos
            </h3>
            <p class="mt-2 text-sm text-gray-600">
                Para tener la app en tu teléfono y usarla más rápido:
            </p>

            <div v-if="isIOS" class="mt-6 space-y-4">
                <div class="flex gap-4 rounded-lg bg-gray-50 p-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">1</span>
                    <div>
                        <p class="font-medium text-gray-900">Tocá el botón Compartir</p>
                        <p class="mt-1 text-sm text-gray-600">En Safari, es el ícono de compartir (cuadrado con flecha hacia arriba) en la barra inferior.</p>
                    </div>
                </div>
                <div class="flex gap-4 rounded-lg bg-gray-50 p-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">2</span>
                    <div>
                        <p class="font-medium text-gray-900">Elegí "Añadir a pantalla de inicio"</p>
                        <p class="mt-1 text-sm text-gray-600">Buscá la opción en el menú y tocá. Luego confirmá con "Añadir".</p>
                    </div>
                </div>
            </div>

            <div v-else class="mt-6 space-y-4">
                <div class="flex gap-4 rounded-lg bg-gray-50 p-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">1</span>
                    <div>
                        <p class="font-medium text-gray-900">Abrí el menú de Chrome</p>
                        <p class="mt-1 text-sm text-gray-600">Tocá los tres puntos (⋮) en la esquina superior derecha del navegador.</p>
                    </div>
                </div>
                <div class="flex gap-4 rounded-lg bg-gray-50 p-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">2</span>
                    <div>
                        <p class="font-medium text-gray-900">Elegí "Instalar app" o "Añadir a pantalla de inicio"</p>
                        <p class="mt-1 text-sm text-gray-600">Según tu versión de Chrome verás una de estas opciones. Confirmá para instalar.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <PrimaryButton type="button" @click="emit('close')">
                    Entendido
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
