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
 * El banner solo se muestra cuando el navegador dispara beforeinstallprompt.
 * Un clic en "Instalar" abre el diálogo nativo y la app se instala sin pasos extra.
 */
export function usePWAInstall() {
    const deferredPrompt = ref(null);
    const showInstallBanner = ref(false);
    const isStandalone = ref(false);

    function requestInstall() {
        if (!deferredPrompt.value) return;
        deferredPrompt.value.prompt();
        deferredPrompt.value.userChoice.then((result) => {
            if (result.outcome === 'accepted') {
                showInstallBanner.value = false;
            }
            deferredPrompt.value = null;
        });
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
    });

    return {
        deferredPrompt,
        showInstallBanner,
        isStandalone,
        requestInstall,
    };
}
