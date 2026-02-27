<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Skeleton from '@/Components/Skeleton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

defineProps({
    invitations: { type: Array, required: true },
});

const showContent = ref(false);
onMounted(() => {
    requestAnimationFrame(() => {
        setTimeout(() => {
            showContent.value = true;
        }, 220);
    });
});

const page = usePage();
const newInvitationUrl = computed(() => page.props.flash?.new_invitation_url ?? null);
const method = ref('whatsapp');
const form = useForm({ email: '', method: 'whatsapp' });

function submit() {
    form.transform((data) => ({ ...data, method: method.value })).post(route('invitations.store'), {
        preserveScroll: true,
        onSuccess: () => {
            if (method.value === 'email') form.reset('email');
            else form.reset('email');
        },
    });
}

function copyUrl() {
    const url = newInvitationUrl.value;
    if (url) navigator.clipboard.writeText(url);
}

function openWhatsApp() {
    const url = newInvitationUrl.value;
    if (!url) return;
    const text = 'Te invito a Chanta Puntos para ver los mismos hijos. Aceptá la invitación acá: ' + url;
    window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
}
</script>

<template>
    <Head title="Invitar a otro padre" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Invitar a otro padre
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <p v-if="page.props.flash?.message" class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-800">
                    {{ page.props.flash.message }}
                </p>
                <p v-if="page.props.flash?.error" class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-800">
                    {{ page.props.flash.error }}
                </p>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <template v-if="!showContent">
                            <div class="mb-4 space-y-2">
                                <Skeleton variant="line" class="w-full" />
                                <Skeleton variant="line" class="w-3/4" />
                            </div>
                            <div class="space-y-4">
                                <Skeleton variant="line" class="h-10 w-full" />
                                <Skeleton variant="line" class="h-10 w-full" />
                                <Skeleton variant="line" class="h-10 w-40" />
                            </div>
                        </template>
                        <template v-else>
                        <p class="mb-4 text-gray-600">
                            Elegí cómo querés invitar al otro padre e ingresá su correo (para identificarlo al aceptar).
                        </p>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <InputLabel value="¿Cómo querés enviar la invitación?" />
                                <div class="mt-2 flex gap-4">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input
                                            v-model="method"
                                            type="radio"
                                            name="method"
                                            value="email"
                                            class="border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="text-sm font-medium text-gray-700">Enviar por correo</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input
                                            v-model="method"
                                            type="radio"
                                            name="method"
                                            value="whatsapp"
                                            class="border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="text-sm font-medium text-gray-700">Compartir por WhatsApp</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="email" value="Correo del invitado" />
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="ejemplo@correo.com"
                                />
                                <p v-if="method === 'email'" class="mt-1 text-xs text-gray-500">
                                    Se le enviará un correo con el enlace para aceptar (configurá SMTP cuando publiques).
                                </p>
                                <p v-if="method === 'whatsapp'" class="mt-1 text-xs text-gray-500">
                                    Se creará un enlace para que lo compartas por WhatsApp; al aceptar, debe usar este correo para iniciar sesión.
                                </p>
                                <InputError :message="form.errors.email" />
                            </div>
                            <PrimaryButton type="submit" :disabled="form.processing">
                                {{ method === 'email' ? 'Enviar invitación por correo' : 'Crear enlace y compartir por WhatsApp' }}
                            </PrimaryButton>
                        </form>

                        <div v-if="newInvitationUrl" class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="mb-2 text-sm font-medium text-gray-700">
                                Enlace para compartir (válido 7 días):
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <input
                                    type="text"
                                    readonly
                                    :value="newInvitationUrl"
                                    class="min-w-0 flex-1 rounded-md border border-gray-300 bg-white text-sm"
                                />
                                <SecondaryButton type="button" @click="copyUrl">
                                    Copiar
                                </SecondaryButton>
                                <PrimaryButton type="button" class="inline-flex items-center gap-2" @click="openWhatsApp">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    Abrir WhatsApp
                                </PrimaryButton>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Al abrir el enlace, la otra persona podrá iniciar sesión o registrarse con el correo que ingresaste y aceptar la invitación.
                            </p>
                        </div>
                        </template>
                    </div>
                </div>

                <div v-if="invitations.length > 0" class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <h3 class="mb-3 text-sm font-medium text-gray-700">
                            Invitaciones pendientes
                        </h3>
                        <ul class="space-y-2">
                            <li
                                v-for="inv in invitations"
                                :key="inv.id"
                                class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-4 py-3"
                            >
                                <span class="text-gray-800">{{ inv.email }}</span>
                                <span class="text-xs text-gray-500">
                                    Expira {{ new Date(inv.expires_at).toLocaleDateString() }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
