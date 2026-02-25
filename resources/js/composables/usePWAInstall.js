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
 * En móvil: muestra el banner de instalar siempre que no sea standalone (aunque el navegador
 * no dispare beforeinstallprompt). Si no hay prompt nativo, al tocar "Instalar" se muestran
 * instrucciones (menú Chrome / Añadir a pantalla de inicio en iOS).
 */
export function usePWAInstall() {
    const deferredPrompt = ref(null);
    const showInstallBanner = ref(false);
    const showInstructionsModal = ref(false);
    const isStandalone = ref(false);
    const isMobile = ref(false);

    function dismissBanner() {
        showInstallBanner.value = false;
        try {
            localStorage.setItem(INSTALL_DISMISSED_KEY, '1');
        } catch (_) {}
    }

    function requestInstall() {
        if (deferredPrompt.value) {
            deferredPrompt.value.prompt();
            deferredPrompt.value.userChoice.then((result) => {
                if (result.outcome === 'accepted') {
                    showInstallBanner.value = false;
                }
                deferredPrompt.value = null;
            });
            return;
        }
        if (isMobile.value) {
            showInstructionsModal.value = true;
        }
    }

    function closeInstructions() {
        showInstructionsModal.value = false;
    }

    onMounted(() => {
        isStandalone.value = isPWAStandalone();
        isMobile.value = isMobileDevice();

        if (isStandalone.value) {
            showInstallBanner.value = false;
            return;
        }

        try {
            if (localStorage.getItem(INSTALL_DISMISSED_KEY)) {
                if (!isMobile.value) {
                    showInstallBanner.value = false;
                    return;
                }
            }
        } catch (_) {}

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt.value = e;
            showInstallBanner.value = true;
        });

        if (isMobile.value) {
            showInstallBanner.value = true;
        }
    });

    return {
        deferredPrompt,
        showInstallBanner,
        showInstructionsModal,
        isStandalone,
        isMobile,
        requestInstall,
        dismissBanner,
        closeInstructions,
    };
}
