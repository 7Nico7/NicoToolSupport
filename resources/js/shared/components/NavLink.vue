<script setup>
// NavLink.vue — para la barra de navegación principal (horizontal)
defineProps({
    href:   { type: String,  default: '#' },
    active: { type: Boolean, default: false },
    icon:   { type: String,  default: null },
});
</script>

<template>
    <a
        :href="href"
        :class="[
            'inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-150',
            active
                ? 'text-brand bg-brand/8 dark:bg-brand/15'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800',
        ]"
    >
        <span v-if="icon" class="material-symbols-outlined text-base">{{ icon }}</span>
        <slot />
    </a>
</template>
