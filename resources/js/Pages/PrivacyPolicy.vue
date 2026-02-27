<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});
</script>

<template>
    <Head title="Política de Privacidad - Chanta Puntos" />
    <div class="min-h-screen flex flex-col bg-gray-50 text-gray-900">
        <!-- Header (mismo que Welcome) -->
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

        <main class="flex-1 px-4 py-10 sm:px-6 md:py-14">
            <article class="mx-auto max-w-3xl prose prose-gray prose-headings:font-semibold prose-headings:text-gray-900 prose-a:text-amber-600 prose-a:no-underline hover:prose-a:underline">
                <h1 class="text-3xl sm:text-4xl text-gray-900 border-b border-gray-200 pb-4">
                    Política de Privacidad
                </h1>
                <p class="text-sm text-gray-500 mt-2">
                    Última actualización: febrero de 2025
                </p>

                <p>
                    Chanta Puntos (&quot;nosotros&quot;, &quot;la app&quot; o &quot;el servicio&quot;) se compromete a proteger la privacidad de los usuarios. Esta política describe qué datos recogemos, para qué los usamos y cómo puedes ejercer tus derechos. Es aplicable a la aplicación web, la PWA y las versiones publicadas en Google Play y App Store.
                </p>

                <h2>1. Responsable del tratamiento</h2>
                <p>
                    Los datos personales son tratados por el responsable del proyecto Chanta Puntos. Para ejercer derechos o consultas sobre privacidad puedes contactarnos mediante el formulario o correo indicado en la app o en esta web.
                </p>

                <h2>2. Datos que recogemos</h2>
                <p>Recogemos únicamente los datos necesarios para prestar el servicio:</p>
                <ul>
                    <li><strong>Cuenta:</strong> nombre, dirección de correo electrónico y contraseña (almacenada de forma segura).</li>
                    <li><strong>Familia e hijos:</strong> nombres y, si lo eliges, iconos o avatares para los perfiles de los menores vinculados a tu cuenta.</li>
                    <li><strong>Uso del servicio:</strong> tareas, puntos, recompensas y actividad relacionada con el sistema de Chantapuntos (validaciones, canjes, etc.).</li>
                    <li><strong>Técnicos:</strong> dirección IP, tipo de navegador y datos de uso de la aplicación cuando sea necesario para el funcionamiento, la seguridad o el cumplimiento legal.</li>
                </ul>

                <h2>3. Finalidad y base legal</h2>
                <p>
                    Utilizamos tus datos para: (a) crear y gestionar tu cuenta y la de tu familia; (b) proporcionar la funcionalidad de puntos, tareas y recompensas; (c) mejorar la app y la experiencia de uso; (d) cumplir obligaciones legales y resolver incidencias. La base legal es la ejecución del contrato (uso del servicio) y, cuando corresponda, tu consentimiento o el interés legítimo del responsable.
                </p>

                <h2>4. Conservación, retención y eliminación de datos</h2>
                <p>
                    Conservamos tus datos mientras mantengas una cuenta activa. Una vez que solicitas la eliminación de la cuenta, borramos los datos personales asociados a ella en un plazo razonable, salvo los que debamos conservar por obligación legal, seguridad o resolución de reclamaciones (en cuyo caso se conservan solo el tiempo mínimo necesario). La desactivación temporal, bloqueo o &quot;congelación&quot; de la cuenta no equivale a eliminación: los datos siguen almacenados hasta que se complete una solicitud de eliminación definitiva.
                </p>
                <p>
                    Puedes solicitar la eliminación de tu cuenta de forma fácilmente accesible desde dentro de la app (por ejemplo, en Ajustes o Perfil) y también desde fuera de la app (por ejemplo, mediante el enlace o formulario de contacto indicado en esta web o en la ficha de la app en Google Play/App Store). Al eliminar la cuenta, se eliminan los datos de usuario asociados a ella, incluidos los perfiles de familia e hijos vinculados.
                </p>

                <h2>5. Seguridad de los datos</h2>
                <p>
                    Aplicamos medidas técnicas y organizativas adecuadas para el manejo seguro de los datos personales y sensibles: protección frente a accesos no autorizados, pérdida, alteración o divulgación indebida. Las contraseñas se almacenan de forma segura (por ejemplo, mediante hash) y el acceso a los datos está limitado a lo necesario para el funcionamiento del servicio.
                </p>

                <h2>6. Cesión y terceros</h2>
                <p>
                    No vendemos ni alquilamos tus datos personales. Solo compartimos información con terceros cuando sea estrictamente necesario (por ejemplo, proveedores de alojamiento o servicios técnicos que nos ayudan a operar la app), bajo compromisos de confidencialidad y en la medida permitida por la ley. No utilizamos SDKs ni código de terceros que no estén aprobados para su uso en servicios dirigidos a familias o menores en el sentido de las políticas de Google Play y App Store. No utilizamos los datos para publicidad dirigida ni mostramos publicidad basada en el perfil del usuario o de menores.
                </p>

                <h2>7. Tus derechos</h2>
                <p>
                    Puedes solicitar el acceso, la rectificación, la supresión, la limitación del tratamiento, la portabilidad de tus datos y oponerte a determinados tratamientos. También puedes presentar una reclamación ante la autoridad de protección de datos de tu país. Para ejercer estos derechos, contacta con nosotros usando los datos de contacto indicados en la app o en esta web.
                </p>

                <h2>8. Cookies y almacenamiento local</h2>
                <p>
                    La app y la web pueden utilizar cookies y almacenamiento local (por ejemplo, para la sesión, preferencias o funcionamiento de la PWA). Puedes configurar tu navegador para limitar o bloquear cookies; ello puede afectar a algunas funciones del servicio.
                </p>

                <h2>9. Menores y uso familiar (incl. menores de 13 años)</h2>
                <p>
                    La app está pensada para que la usen principalmente padres y madres, pero en un contexto familiar es posible que menores (incluidos menores de 13 años) tengan acceso a la app o a perfiles vinculados a la cuenta del adulto. En ese caso:
                </p>
                <ul>
                    <li>La cuenta principal la crea y gestiona un adulto (padre, madre o tutor). No recabamos datos personales directamente de menores de 13 años sin que un adulto haya creado o autorizado el perfil familiar.</li>
                    <li>La información relativa a los menores (por ejemplo, nombre, avatar o icono, puntos y tareas) se recoge y trata en el marco de la cuenta del adulto responsable. Dicha recogida se realiza bajo el control y, cuando la ley lo exija, con el consentimiento del padre, madre o tutor (por ejemplo, en cumplimiento de normativas como COPPA u otras aplicables en tu país).</li>
                    <li>No utilizamos los datos de menores para publicidad dirigida ni incluimos SDKs no aprobados para servicios dirigidos a niños o familias según las políticas de Google Play y App Store.</li>
                    <li>Nuestra app, cuando está dirigida a audiencias que incluyen menores, cumple con las políticas de Google Play para familias y con la legislación aplicable en materia de protección de datos de menores.</li>
                </ul>

                <h2>10. Cambios en esta política</h2>
                <p>
                    Podemos actualizar esta política de privacidad. Los cambios relevantes se comunicarán mediante un aviso en la app o por correo cuando sea necesario. La fecha de &quot;Última actualización&quot; al inicio del documento refleja la última revisión.
                </p>

                <h2>11. Contacto y responsable</h2>
                <p>
                    El responsable del tratamiento es el desarrollador o entidad que figura como titular de la aplicación &quot;Chanta Puntos&quot; en las tiendas Google Play y App Store. Para consultas sobre privacidad, ejercicio de derechos (acceso, rectificación, supresión, portabilidad, oposición) o para solicitar la eliminación de tu cuenta desde fuera de la app, utiliza el canal de contacto disponible en la aplicación o en esta web (formulario o correo electrónico indicado). Responderemos a las solicitudes en un plazo razonable según la normativa aplicable.
                </p>
            </article>
        </main>

        <!-- Footer (mismo que Welcome, con enlace a esta página) -->
        <footer class="border-t border-gray-200 bg-white px-4 py-8">
            <div class="mx-auto flex max-w-4xl flex-col items-center justify-between gap-4 sm:flex-row">
                <span class="text-base text-gray-500">Chanta Puntos — Puntos y recompensas para la familia</span>
                <nav v-if="canLogin" class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
                    <Link
                        :href="route('privacy')"
                        class="text-base font-medium text-gray-600 hover:text-amber-600"
                    >
                        Política de Privacidad
                    </Link>
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
