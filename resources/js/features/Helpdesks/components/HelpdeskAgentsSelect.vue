<script setup>
// resources/js/features/helpdesks/components/HelpdeskAgentsSelect.vue
//
// Selector múltiple de agentes con búsqueda interna.
// Muestra lista de agentes disponibles con checkboxes.
// Reutilizable en Form.vue del módulo.

import { ref, computed } from 'vue'
import TextInput from '@/shared/components/TextInput.vue'

const props = defineProps({
    // IDs de agentes actualmente seleccionados
    modelValue: { type: Array, default: () => [] },
    // Lista completa de agentes disponibles { id, name, email, role }
    agents:     { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue'])

const search = ref('')

const filteredAgents = computed(() => {
    const q = search.value.toLowerCase().trim()
    if (!q) return props.agents
    return props.agents.filter(a =>
        a.name.toLowerCase().includes(q) ||
        a.email?.toLowerCase().includes(q)
    )
})

const isSelected = (agentId) => props.modelValue.includes(agentId)

const toggle = (agentId) => {
    const current = [...props.modelValue]
    const idx = current.indexOf(agentId)
    if (idx === -1) {
        current.push(agentId)
    } else {
        current.splice(idx, 1)
    }
    emit('update:modelValue', current)
}

const toggleAll = () => {
    if (props.modelValue.length === props.agents.length) {
        emit('update:modelValue', [])
    } else {
        emit('update:modelValue', props.agents.map(a => a.id))
    }
}

const ROLE_LABEL = {
    admin:       { label: 'Admin',  class: 'bg-brand/10 text-brand' },
    agent:       { label: 'Agente', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    super_admin: { label: 'Super',  class: 'bg-slate-100 text-slate-500' },
}
</script>

<template>
    <div class="flex flex-col gap-3">

        <!-- Barra superior: búsqueda + contador + seleccionar todos -->
        <div class="flex items-center gap-3">
            <div class="flex-1">
                <TextInput v-model="search" icon="search" placeholder="Buscar agente..." />
            </div>

            <button
                type="button"
                class="shrink-0 text-xs font-bold text-brand hover:opacity-70 transition-opacity whitespace-nowrap"
                @click="toggleAll"
            >
                {{ modelValue.length === agents.length ? 'Deseleccionar todos' : 'Seleccionar todos' }}
            </button>
        </div>

        <!-- Contador seleccionados -->
        <p class="text-[11px] text-slate-500">
            <span class="font-bold text-slate-700 dark:text-slate-300">{{ modelValue.length }}</span>
            de {{ agents.length }} agentes seleccionados
        </p>

        <!-- Lista de agentes -->
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden max-h-72 overflow-y-auto custom-scrollbar">

            <label
                v-for="agent in filteredAgents"
                :key="agent.id"
                class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-colors border-b border-slate-100 dark:border-slate-800 last:border-0"
                :class="isSelected(agent.id)
                    ? 'bg-brand/5 dark:bg-brand/10'
                    : 'hover:bg-slate-50 dark:hover:bg-slate-800/50'"
            >
                <!-- Checkbox -->
                <input
                    type="checkbox"
                    :checked="isSelected(agent.id)"
                    class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-brand focus:ring-brand/40 bg-white dark:bg-slate-800"
                    @change="toggle(agent.id)"
                />

                <!-- Avatar -->
                <div
                    :class="['w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black shrink-0 transition-colors',
                        isSelected(agent.id) ? 'bg-brand text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'
                    ]"
                >
                    {{ agent.name.charAt(0).toUpperCase() }}
                </div>

                <!-- Info del agente -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ agent.name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ agent.email }}</p>
                </div>

                <!-- Badge de rol -->
                <span
                    v-if="ROLE_LABEL[agent.role]"
                    :class="['text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded shrink-0', ROLE_LABEL[agent.role].class]"
                >
                    {{ ROLE_LABEL[agent.role].label }}
                </span>
            </label>

            <!-- Empty state de búsqueda -->
            <div v-if="filteredAgents.length === 0" class="flex flex-col items-center py-8 text-slate-400 gap-2">
                <span class="material-symbols-outlined text-2xl">person_search</span>
                <p class="text-xs">Sin resultados para "{{ search }}"</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-200 dark:bg-slate-700 rounded-full;
}
</style>
