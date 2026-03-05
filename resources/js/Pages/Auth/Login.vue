<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

function getLoginUrl() {
    try {
        if (typeof route === 'function') return route('login');
    } catch (_) {}
    return window.location.pathname === '/login' ? '/login' : '/login';
}

const submit = () => {
    form.post(getLoginUrl(), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Botón verde ancho completo -->
            <div class="mt-6">
                <button
                    type="submit"
                    class="flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition duration-150 ease-in-out hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25"
                    :disabled="form.processing"
                >
                    Iniciar sesión
                </button>
            </div>

            <!-- Recuperar contraseña centrado -->
            <div v-if="canResetPassword" class="mt-4 text-center">
                <Link
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>
        </form>

        <!-- Enlace de registro fuera de la tarjeta -->
        <template #below>
            <p class="mt-6 text-center text-sm text-gray-600">
                Aún no tenés una cuenta:
                <Link
                    :href="route('register')"
                    class="font-semibold text-indigo-600 underline decoration-indigo-400/60 underline-offset-2 hover:text-indigo-800 focus:outline-none"
                >
                    Creala ahora
                </Link>
            </p>
        </template>
    </GuestLayout>
</template>
