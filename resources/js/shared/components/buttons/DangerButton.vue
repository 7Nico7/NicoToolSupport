<script setup>
defineProps({
    type:     { type: String,  default: 'button' },
    disabled: { type: Boolean, default: false },
    loading:  { type: Boolean, default: false },
    size:     { type: String,  default: 'md' },
    icon:     { type: String,  default: null },
    variant:  { type: String,  default: 'solid' }, // solid | ghost
});
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="[
            'inline-flex items-center justify-center gap-2 font-semibold rounded-xl',
            'focus:outline-none focus:ring-2 focus:ring-danger/40 focus:ring-offset-2',
            'disabled:opacity-50 disabled:cursor-not-allowed',
            'transition-all duration-150 active:scale-[0.97]',
            variant === 'solid'
                ? 'bg-danger text-white hover:bg-red-600 active:bg-red-700'
                : 'text-danger hover:bg-red-50 dark:hover:bg-red-900/20',
            {
                'px-3 py-1.5 text-xs': size === 'sm',
                'px-4 py-2 text-sm':   size === 'md',
                'px-5 py-2.5 text-base': size === 'lg',
            },
        ]"
    >
        <span
            v-if="loading"
            class="material-symbols-outlined animate-spin"
            :class="{ 'text-sm': size === 'sm', 'text-base': size === 'md', 'text-lg': size === 'lg' }"
        >progress_activity</span>
        <span
            v-else-if="icon"
            class="material-symbols-outlined"
            :class="{ 'text-sm': size === 'sm', 'text-base': size === 'md', 'text-lg': size === 'lg' }"
        >{{ icon }}</span>
        <slot />
    </button>
</template>
