<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'success', 'warning', 'danger', 'info', 'secondary'].includes(value),
    },
    dot: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = computed(() => {
    const base = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium';
    const variants = {
        primary: 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
        success: 'bg-success/10 text-success-700 dark:bg-success/20 dark:text-success-400',
        warning: 'bg-warning/10 text-warning-700 dark:bg-warning/20 dark:text-warning-400',
        danger: 'bg-danger/10 text-danger-700 dark:bg-danger/20 dark:text-danger-400',
        info: 'bg-info/10 text-info-700 dark:bg-info/20 dark:text-info-400',
        secondary: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
    return `${base} ${variants[props.variant]}`;
});

const dotColor = computed(() => {
    const colors = {
        primary: 'bg-primary-500 dark:bg-primary-400',
        success: 'bg-success-500 dark:bg-success-400',
        warning: 'bg-warning-500 dark:bg-warning-400',
        danger: 'bg-danger-500 dark:bg-danger-400',
        info: 'bg-info-500 dark:bg-info-400',
        secondary: 'bg-slate-500 dark:bg-slate-400',
    };
    return colors[props.variant];
});
</script>

<template>
    <span :class="variantClasses">
        <span v-if="dot" :class="['size-1.5 rounded-full', dotColor]"></span>
        <slot />
    </span>
</template>
