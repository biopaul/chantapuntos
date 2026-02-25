import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const SPLASH_DURATION_MS = 1000;
const SPLASH_BG = '#e8e8e8';

if (typeof navigator !== 'undefined' && 'serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

function showSplash(el) {
    el.innerHTML = `
        <div id="pwa-splash" style="
            position: fixed; inset: 0; z-index: 99999;
            background: ${SPLASH_BG};
            display: flex; align-items: center; justify-content: center;
        ">
            <img src="/images/splash.png" alt="Chanta Puntos" style="
                max-width: 90%; max-height: 80%; object-fit: contain;
            " onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div style="display: none; text-align: center; color: #333; font-size: 1.5rem; font-weight: 600;">
                Chanta Puntos
            </div>
        </div>
    `;
}

function preloadPageChunks() {
    const glob = import.meta.glob('./Pages/**/*.vue');
    return Promise.all(Object.keys(glob).map((key) => glob[key]()));
}

function prefetchRoutes() {
    if (typeof window === 'undefined' || !window.route) return Promise.resolve();
    const routes = ['dashboard', 'history.index', 'profile.edit', 'redemptions.create', 'actions.index', 'invitations.index', 'onboarding'];
    return Promise.all(
        routes.map((name) => {
            try {
                const url = window.route(name);
                return url ? fetch(url, { method: 'GET', credentials: 'same-origin' }).catch(() => {}) : Promise.resolve();
            } catch (_) {
                return Promise.resolve();
            }
        })
    );
}

function runSplashAndPreload() {
    const el = document.getElementById('app');
    if (!el) return Promise.resolve();

    showSplash(el);

    const preloadPromise = Promise.all([
        preloadPageChunks(),
        prefetchRoutes(),
    ]);
    const minDelayPromise = new Promise((r) => setTimeout(r, SPLASH_DURATION_MS));

    return Promise.all([preloadPromise, minDelayPromise]).then(() => {
        el.innerHTML = '';
    });
}

runSplashAndPreload().then(() => {
    createInertiaApp({
        title: (title) => `${title} - ${appName}`,
        resolve: (name) =>
            resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue'),
            ),
        setup({ el, App, props, plugin }) {
            return createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue)
                .mount(el);
        },
        progress: {
            color: '#4B5563',
        },
    });
});
