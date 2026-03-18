<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: { type: String, default: null },
    icon: { type: String, default: null },
    danger: { type: Boolean, default: false },
    active: { type: Boolean, default: false },

    as: { type: String, default: Link },
    method: { type: String, default: 'get' }
})
</script>

<template>
    <component
        :is="as"
        :href="href"
        :method="method"
        :class="[
            'flex w-full items-center gap-2.5 px-4 py-2 text-sm transition-colors duration-100',
            'focus:outline-none',
            danger
                ? 'text-danger hover:bg-red-50 dark:hover:bg-red-900/20'
                : active
                    ? 'bg-brand/5 text-brand font-semibold'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50',
        ]"
    >
        <span v-if="icon" class="material-symbols-outlined text-base opacity-70">{{ icon }}</span>
        <slot />
    </component>
</template>
