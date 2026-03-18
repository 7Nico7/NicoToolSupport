<script setup>
/**
 * FilterButton — Botón estandarizado para abrir el Sidebar de filtros.
 */
defineProps({
    activeCount: {
        type: Number,
        default: 0
    }
})

defineEmits(['click'])
</script>

<template>
    <button
        type="button"
        @click="$emit('click')"
        class="relative inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:border-brand hover:text-brand transition-all group"
    >
        <span class="material-symbols-outlined text-[18px] transition-transform group-hover:rotate-12">
            filter_alt
        </span>

        <span>Filtros</span>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-0"
            enter-to-class="transform scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform scale-100"
            leave-to-class="transform scale-0"
        >
            <span
                v-if="activeCount > 0"
                class="absolute -top-1.5 -right-1.5 h-5 min-w-[20px] px-1.5 rounded-full text-[10px] bg-brand text-white flex items-center justify-center font-black shadow-sm ring-2 ring-white dark:ring-slate-900"
            >
                {{ activeCount }}
            </span>
        </Transition>
    </button>
</template>
