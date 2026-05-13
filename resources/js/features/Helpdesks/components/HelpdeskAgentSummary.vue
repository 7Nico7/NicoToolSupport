<script setup>
/**
 * Componente de resumen lateral para mostrar los agentes
 * seleccionados en tiempo real.
 */
defineProps({
    // IDs de los agentes seleccionados (form.agent_ids)
    selectedIds: {
        type: Array,
        default: () => []
    },
    // Lista completa de agentes para buscar nombres e iniciales
    agents: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <div class="bg-brand/5 border border-brand/10 rounded-2xl p-5 shadow-sm">
        <header class="flex items-center justify-between mb-4">
            <p class="text-[10px] font-black text-brand uppercase tracking-widest">
                Agentes seleccionados
            </p>
            <span
                class="ml-1 px-1.5 py-0.5 bg-brand/15 text-brand rounded-full text-[10px] font-bold"
                v-if="selectedIds.length > 0"
            >
                {{ selectedIds.length }}
            </span>
        </header>

        <div v-if="selectedIds.length > 0" class="flex flex-wrap gap-2">
            <span
                v-for="agentId in selectedIds"
                :key="agentId"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-brand/20 text-xs font-semibold text-slate-700 dark:text-slate-300 shadow-sm"
            >
                <span class="w-4 h-4 rounded-full bg-brand text-white flex items-center justify-center text-[9px] font-black shrink-0 uppercase">
                    {{ agents.find(a => a.id === agentId)?.name?.charAt(0) ?? '?' }}
                </span>

                {{ agents.find(a => a.id === agentId)?.name ?? '—' }}
            </span>
        </div>

        <div v-else class="flex flex-col items-center py-6 text-slate-400 gap-2 text-center">
            <span class="material-symbols-outlined text-2xl opacity-50">person_off</span>
            <p class="text-xs font-medium">Ningún agente seleccionado aún.</p>
        </div>

        <footer class="mt-4 pt-4 border-t border-brand/10">
            <p class="text-[10px] text-brand/60 leading-relaxed italic text-center">
                Los cambios se aplicarán al guardar el formulario.
            </p>
        </footer>
    </div>
</template>
