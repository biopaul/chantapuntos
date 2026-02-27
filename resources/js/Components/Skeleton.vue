<script setup>
/**
 * Placeholder de carga (skeleton) con animación gris/blanco.
 * Uso: <Skeleton class="h-4 w-32" /> o <Skeleton variant="circle" class="h-12 w-12" />
 */
defineProps({
    /** 'line' (por defecto), 'block', 'circle' */
    variant: {
        type: String,
        default: 'block',
        validator: (v) => ['line', 'block', 'circle'].includes(v),
    },
    /** Clases adicionales (ej. h-4 w-32 para tamaño). Pasar como :skeleton-class o class. */
    skeletonClass: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div
        class="skeleton-shimmer rounded"
        :class="[
            variant === 'circle' ? 'rounded-full' : 'rounded',
            variant === 'line' ? 'h-4' : '',
            skeletonClass,
        ]"
    />
</template>

<style scoped>
.skeleton-shimmer {
    background: linear-gradient(
        90deg,
        rgb(229 231 235) 0%,
        rgb(243 244 246) 50%,
        rgb(229 231 235) 100%
    );
    background-size: 200% 100%;
    animation: skeleton-shimmer 1.5s ease-in-out infinite;
}

@keyframes skeleton-shimmer {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>
