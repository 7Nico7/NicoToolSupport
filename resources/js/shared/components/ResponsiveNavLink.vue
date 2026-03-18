<script setup>
// ResponsiveNavLink.vue — para el menú mobile (vertical)
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
            'flex w-full items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-150',
            'border-l-4',
            active
                ? 'border-brand text-brand bg-brand/5 dark:bg-brand/10'
                : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300',
        ]"
    >
        <span v-if="icon" class="material-symbols-outlined text-lg">{{ icon }}</span>
        <slot />
    </a>
</template>
