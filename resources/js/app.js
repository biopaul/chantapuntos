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

/** Detecta si la app se está ejecutando como PWA instalada (standalone), no en el navegador. */
function isPWA() {
    if (typeof window === 'undefined') return false;
    if (window.matchMedia('(display-mode: standalone)').matches) return true;
    if (window.navigator.standalone === true) return true; // iOS
    return false;
}

/** Obtiene el nombre del componente de la página inicial (Inertia data-page). */
function getInitialPageComponent() {
    const el = document.getElementById('app');
    const dataPage = el && el.getAttribute('data-page');
    if (!dataPage) return null;
    try {
        const page = JSON.parse(dataPage);
        return page && page.component ? page.component : null;
    } catch (_) {
        return null;
    }
}

/** Path base de la app (ej. /puntoschanta/public) para no depender de la raíz del host. */
function getAppBasePath() {
    if (typeof window === 'undefined') return '';
    const pathname = window.location.pathname || '';
    const parts = pathname.split('/').filter(Boolean);
    const publicIndex = parts.indexOf('public');
    return publicIndex >= 0 ? '/' + parts.slice(0, publicIndex + 1).join('/') : '';
}

/** Muestra el overlay de splash solo en PWA y solo en Dashboard. */
function showSplash(el) {
    const base = getAppBasePath();
    const splashSrc = base ? `${base}/images/splash.png` : '/images/splash.png';
    el.innerHTML = `
        <div id="pwa-splash" style="
            position: fixed; inset: 0; z-index: 99999;
            background: ${SPLASH_BG};
            display: flex; align-items: center; justify-content: center;
        ">
            <img src="${splashSrc}" alt="Chanta Puntos" style="
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
    const routes = ['dashboard', 'history.index', 'profile.edit', 'redemptions.create', 'actions.index', 'invitations.index', 'onboarding', 'settings.index'];
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

    const initialComponent = getInitialPageComponent();
    const showSplashThisLoad = isPWA() && initialComponent === 'Dashboard';

    if (showSplashThisLoad) {
        showSplash(el);
    }

    const preloadPromise = Promise.all([
        preloadPageChunks().catch(() => {}),
        prefetchRoutes(),
    ]);
    const minDelayPromise = showSplashThisLoad
        ? new Promise((r) => setTimeout(r, SPLASH_DURATION_MS))
        : Promise.resolve();

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
