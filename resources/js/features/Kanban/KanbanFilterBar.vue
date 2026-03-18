<script setup>
// resources/js/features/Kanban/KanbanFilterBar.vue
// Responsabilidad: mostrar botón "Filtros" + chips de filtros activos.
// NO dispara fetch directamente — usa store.applyFilter que lo encapsula.

import FilterButton from '@/shared/components/buttons/FilterButton.vue';
import { useKanbanStore } from '@/features/Kanban/stores/kanbanStore';

const props = defineProps({
    priorities: { type: Array, default: () => [] },
    agents:     { type: Array, default: () => [] },
    projects:   { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    types:      { type: Array, default: () => [] },
});

const emit = defineEmits(['open-sidebar']);

const store = useKanbanStore();

// Solo las claves con soporte de chip — evita iterar status_id (sin UI de sidebar)
//    y cualquier clave futura que el store agregue sin soporte visual aquí.
const CHIP_KEYS = ['search', 'priority_id', 'project_id', 'category_id', 'type_id', 'assigned_to'];

const resolveLabel = (key, value) => {
    if (key === 'priority_id') return props.priorities.find(p => p.id == value)?.name ?? value;
    if (key === 'assigned_to') return props.agents.find(a     => a.id == value)?.name ?? value;
    if (key === 'project_id')  return props.projects.find(p   => p.id == value)?.name ?? value;
    if (key === 'category_id') return props.categories.find(c => c.id == value)?.name ?? value;
    if (key === 'type_id')     return props.types.find(t      => t.id == value)?.name ?? value;
    if (key === 'search')      return `"${value}"`;
    return value;
};

// store.applyFilter — acción atómica que muta el filtro y recarga.
//    Antes: store.setFilter(key, null) + store.fetchTickets() duplicado aquí
//    y en Index.vue. Ahora hay un solo lugar donde esto ocurre: el store.
const removeFilter = (key) => store.applyFilter(key, null);
</script>

<template>
    <div class="flex items-center gap-2 flex-wrap">

        <FilterButton
            :active-count="store.activeFilterCount"
            @click="$emit('open-sidebar')"
        />

        <!-- Itera CHIP_KEYS — no store.filters completo.
             Evita mostrar chip para status_id (no tiene soporte de resolveLabel
             ni se pasa :statuses a este componente). -->
        <template v-for="key in CHIP_KEYS" :key="key">
            <span
                v-if="store.filters[key] !== null && store.filters[key] !== ''"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                       text-[10px] font-semibold bg-brand/10 text-brand border border-brand/20"
            >
                {{ resolveLabel(key, store.filters[key]) }}
                <button type="button" @click="removeFilter(key)" class="hover:opacity-60 transition-opacity">
                    <span class="material-symbols-outlined text-[12px]">close</span>
                </button>
            </span>
        </template>

    </div>
</template>
