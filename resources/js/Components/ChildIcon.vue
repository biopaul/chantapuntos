<script setup>
const props = defineProps({
    icon: { type: String, default: 'star' },
    size: { type: String, default: 'md' },
    avatarUrl: { type: String, default: null },
});

const iconEmoji = {
    star: '⭐',
    heart: '❤️',
    sun: '☀️',
    moon: '🌙',
    cat: '🐱',
    dog: '🐕',
    fish: '🐟',
    bird: '🐦',
    balloon: '🎈',
    gift: '🎁',
    cake: '🎂',
    music: '🎵',
    book: '📖',
    pencil: '✏️',
    flower: '🌸',
    rainbow: '🌈',
};

const sizeClasses = {
    sm: 'w-8 h-8 text-xl',
    md: 'w-12 h-12 text-2xl',
    lg: 'w-16 h-16 text-3xl',
    xl: 'w-20 h-20 text-4xl',
};

const displayIcon = iconEmoji[props.icon] ?? '⭐';
</script>

<template>
    <div
        v-if="avatarUrl"
        class="inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-200"
        :class="sizeClasses[size]"
    >
        <img :src="avatarUrl" :alt="icon" class="h-full w-full object-cover" />
    </div>
    <div
        v-else
        class="inline-flex shrink-0 items-center justify-center rounded-full bg-gray-100"
        :class="sizeClasses[size]"
    >
        {{ displayIcon }}
    </div>
</template>
