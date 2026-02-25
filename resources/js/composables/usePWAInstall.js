import { onMounted, ref } from 'vue';

const INSTALL_DISMISSED_KEY = 'chanta-puntos-install-dismissed';

/** Detecta si el dispositivo es móvil (touch-first o userAgent). */
export function isMobileDevice() {
    if (typeof navigator === 'undefined') return false;
    const ua = navigator.userAgent || '';
    const hasTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    const mobileMatch = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|mobile/i.test(ua);
    return mobileMatch || (hasTouch && window.matchMedia('(max-width: 1024px)').matches);
}

/** Detecta si es iOS (Safari). */
export function isIOS() {
    if (typeof navigator === 'undefined') return false;
    return /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
}

/** Detecta si la app se ejecuta como PWA instalada (standalone). */
export function isPWAStandalone() {
    if (typeof window === 'undefined') return false;
    return (
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true
    );
}

/**
 * Composable para instalación PWA.
 * El banner se muestra:
 * - En móvil: cuando la app no está instalada (no standalone).
 * - En desktop: cuando el navegador dispara beforeinstallprompt.
 * Un clic en "Instalar" abre el diálogo nativo (si está disponible) y la app se instala sin pasos extra.
 */
export function usePWAInstall() {
    const deferredPrompt = ref(null);
    const showInstallBanner = ref(false);
    const showFallbackHint = ref(false);
    const isStandalone = ref(false);

    function requestInstall() {
        if (deferredPrompt.value) {
            deferredPrompt.value.prompt();
            deferredPrompt.value.userChoice.then((result) => {
                if (result.outcome === 'accepted') {
                    showInstallBanner.value = false;
                }
                deferredPrompt.value = null;
            });
            showFallbackHint.value = false;
            return;
        }
        showFallbackHint.value = true;
        setTimeout(() => {
            showFallbackHint.value = false;
        }, 8000);
    }

    function dismissFallbackHint() {
        showFallbackHint.value = false;
    }

    onMounted(() => {
        isStandalone.value = isPWAStandalone();

        if (isStandalone.value) {
            showInstallBanner.value = false;
            return;
        }

        try {
            if (localStorage.getItem(INSTALL_DISMISSED_KEY)) {
                showInstallBanner.value = false;
                return;
            }
        } catch (_) {}

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt.value = e;
            showInstallBanner.value = true;
        });

        const isMobile = isMobileDevice();
        if (isMobile) {
            showInstallBanner.value = true;
        }
    });

    const fallbackHintText = isIOS()
        ? 'En Safari: tocá Compartir y luego «Añadir a pantalla de inicio».'
        : 'En Chrome: tocá el menú ⋮ y elegí «Instalar app» o «Añadir a pantalla de inicio».';

    return {
        deferredPrompt,
        showInstallBanner,
        showFallbackHint,
        fallbackHintText,
        isStandalone,
        requestInstall,
        dismissFallbackHint,
    };
}
