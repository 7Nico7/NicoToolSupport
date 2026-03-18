<script setup>
defineProps({
    // Variantes visuales
    variant:  { type: String, default: 'default' }, // default | flat | outlined | elevated
    padding:  { type: String, default: 'md' },      // none | sm | md | lg
    // Comportamiento
    hoverable: { type: Boolean, default: false },
    clickable: { type: Boolean, default: false },
    // Borde de color (ej. para indicadores de estado)
    accent:    { type: String, default: null },  // color CSS ej. '#2563eb'
    accentSide:{ type: String, default: 'left' }, // left | top
});
</script>

<template>
    <div
        :class="[
            'relative rounded-2xl overflow-hidden transition-all duration-200',
            // Variantes
            variant === 'default'  && 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm',
            variant === 'flat'     && 'bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50',
            variant === 'outlined' && 'bg-transparent border-2 border-slate-200 dark:border-slate-700',
            variant === 'elevated' && 'bg-white dark:bg-slate-800 shadow-lg shadow-slate-900/8 dark:shadow-slate-900/30',
            // Padding
            padding === 'none' && 'p-0',
            padding === 'sm'   && 'p-3',
            padding === 'md'   && 'p-5',
            padding === 'lg'   && 'p-7',
            // Interactividad
            hoverable && 'hover:-translate-y-0.5 hover:shadow-md cursor-pointer',
            clickable && 'cursor-pointer active:scale-[0.99]',
        ]"
    >
        <!-- Accent border left -->
        <div
            v-if="accent && accentSide === 'left'"
            class="absolute left-0 top-0 bottom-0 w-1"
            :style="{ backgroundColor: accent }"
        />
        <!-- Accent border top -->
        <div
            v-if="accent && accentSide === 'top'"
            class="absolute left-0 top-0 right-0 h-0.5"
            :style="{ backgroundColor: accent }"
        />

        <!-- Header slot -->
        <div v-if="$slots.header" class="mb-4">
            <slot name="header" />
        </div>

        <!-- Default content -->
        <slot />

        <!-- Footer slot -->
        <div v-if="$slots.footer" :class="['mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50']">
            <slot name="footer" />
        </div>
    </div>
</template>
