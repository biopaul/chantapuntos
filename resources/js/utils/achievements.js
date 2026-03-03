/**
 * Sistema de Logros — Chanta Puntos
 * Lógica puramente basada en total_points_earned (puntos acumulados históricos).
 */

export const STAGE_KEYS = {
    CHANTA: 'chanta',
    DESPERTANDO: 'despertando',
    LLAMA: 'llama',
    LEGENDARIA: 'legendaria',
};

export const ACHIEVEMENTS = [
    // Etapa 1 – Modo Chanta
    { points: 10,  name: 'Despertando',          stage: 'Modo Chanta',       stageKey: STAGE_KEYS.CHANTA,      color: 'yellow', level: 1  },
    { points: 30,  name: 'Primer Movimiento',     stage: 'Modo Chanta',       stageKey: STAGE_KEYS.CHANTA,      color: 'yellow', level: 2  },
    { points: 50,  name: 'Cambio de Ritmo',       stage: 'Modo Chanta',       stageKey: STAGE_KEYS.CHANTA,      color: 'yellow', level: 3  },
    // Etapa 2 – Despertando Llama
    { points: 75,  name: 'En Construcción',       stage: 'Despertando Llama', stageKey: STAGE_KEYS.DESPERTANDO, color: 'orange', level: 4  },
    { points: 100, name: 'Llama en Práctica',     stage: 'Despertando Llama', stageKey: STAGE_KEYS.DESPERTANDO, color: 'orange', level: 5  },
    { points: 150, name: 'Compromiso Activado',   stage: 'Despertando Llama', stageKey: STAGE_KEYS.DESPERTANDO, color: 'orange', level: 6  },
    // Etapa 3 – Modo Llama
    { points: 200, name: 'Llama Estratégica',     stage: 'Modo Llama',        stageKey: STAGE_KEYS.LLAMA,       color: 'blue',   level: 7  },
    { points: 250, name: 'Llama en Racha',        stage: 'Modo Llama',        stageKey: STAGE_KEYS.LLAMA,       color: 'blue',   level: 8  },
    { points: 300, name: 'Llama Referente',       stage: 'Modo Llama',        stageKey: STAGE_KEYS.LLAMA,       color: 'blue',   level: 9  },
    { points: 350, name: 'Llama Imparable',       stage: 'Modo Llama',        stageKey: STAGE_KEYS.LLAMA,       color: 'blue',   level: 10 },
    // Etapa 4 – Llama Legendaria
    { points: 400, name: 'Llama Destacada',       stage: 'Llama Legendaria',  stageKey: STAGE_KEYS.LEGENDARIA,  color: 'red',    level: 11 },
    { points: 450, name: 'Llama Maestra',         stage: 'Llama Legendaria',  stageKey: STAGE_KEYS.LEGENDARIA,  color: 'red',    level: 12 },
    { points: 500, name: 'Llama Legendaria',      stage: 'Llama Legendaria',  stageKey: STAGE_KEYS.LEGENDARIA,  color: 'red',    level: 13 },
];

export const MAX_POINTS = 500;

/**
 * Devuelve el logro actual (el último desbloqueado), o null si no llegó a 10 pts.
 */
export function getCurrentAchievement(totalPts) {
    const pts = totalPts ?? 0;
    let current = null;
    for (const a of ACHIEVEMENTS) {
        if (pts >= a.points) current = a;
        else break;
    }
    return current;
}

/**
 * Devuelve el próximo logro a desbloquear, o null si ya es Llama Legendaria.
 */
export function getNextAchievement(totalPts) {
    const pts = totalPts ?? 0;
    for (const a of ACHIEVEMENTS) {
        if (pts < a.points) return a;
    }
    return null;
}

/**
 * Porcentaje de progreso relativo entre el logro actual y el siguiente (0–100).
 * Útil para la barra de progreso horizontal en las tarjetas del Dashboard.
 */
export function getProgressPercent(totalPts) {
    const pts = totalPts ?? 0;
    const next = getNextAchievement(pts);
    if (!next) return 100;

    const current = getCurrentAchievement(pts);
    const from = current ? current.points : 0;
    const to = next.points;

    return Math.round(((pts - from) / (to - from)) * 100);
}

/**
 * Porcentaje para el termómetro vertical de la ficha pública (0–100).
 * Basado en totalPts / MAX_POINTS.
 */
export function getThermometerPercent(totalPts) {
    const pts = totalPts ?? 0;
    return Math.min(Math.round((pts / MAX_POINTS) * 100), 100);
}

/**
 * Cuántos puntos faltan para el próximo logro.
 */
export function getPointsToNext(totalPts) {
    const pts = totalPts ?? 0;
    const next = getNextAchievement(pts);
    return next ? next.points - pts : 0;
}

/**
 * Nombre de display con prefijo de nivel: "Nivel 1 - Despertando"
 */
export function getDisplayName(achievement) {
    return `Nivel ${achievement.level} - ${achievement.name}`;
}

/**
 * Clases de Tailwind por color de etapa.
 */
export const STAGE_COLORS = {
    yellow: {
        bg: 'bg-yellow-100',
        text: 'text-yellow-700',
        border: 'border-yellow-300',
        ring: 'ring-yellow-400',
        dot: 'bg-yellow-400',
        badge: 'bg-yellow-500',
    },
    orange: {
        bg: 'bg-orange-100',
        text: 'text-orange-700',
        border: 'border-orange-300',
        ring: 'ring-orange-400',
        dot: 'bg-orange-400',
        badge: 'bg-orange-500',
    },
    blue: {
        bg: 'bg-blue-100',
        text: 'text-blue-700',
        border: 'border-blue-300',
        ring: 'ring-blue-400',
        dot: 'bg-blue-400',
        badge: 'bg-blue-500',
    },
    red: {
        bg: 'bg-red-100',
        text: 'text-red-700',
        border: 'border-red-300',
        ring: 'ring-red-400',
        dot: 'bg-red-400',
        badge: 'bg-red-500',
    },
};
