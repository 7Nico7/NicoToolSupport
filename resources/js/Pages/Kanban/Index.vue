<script setup>
// resources/js/Pages/Kanban/Index.vue

import { ref, onMounted, computed } from 'vue'
import { Head } from '@inertiajs/vue3'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import KanbanColumn from '@/features/Kanban/KanbanColumn.vue'
import KanbanFilterBar from '@/features/Kanban/KanbanFilterBar.vue'
import CreateTicketModal from '@/features/Kanban/Modals/CreateTicketModal.vue'
import EditTicketModal from '@/features/Kanban/Modals/EditTicketModal.vue'
import SidebarFilter from '@/shared/components/SidebarFilter.vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import TextInput from '@/shared/components/TextInput.vue'
import SelectInput from '@/shared/components/SelectInput.vue'

import { useKanbanStore } from '@/features/Kanban/stores/kanbanStore'
import { useAuth } from '@/shared/composables/useAuth'

/* ─────────────────────────────────────────────────────────
 * Props (Inertia)
 * ───────────────────────────────────────────────────────── */
const props = defineProps({
    statuses:   { type: Array,  default: () => [] },
    priorities: { type: Array,  default: () => [] },
    types:      { type: Array,  default: () => [] },
    categories: { type: Array,  default: () => [] },
    projects:   { type: Array,  default: () => [] },
    helpdesks:  { type: Array,  default: () => [] },
    agents:     { type: Array,  default: () => [] },
    can:        { type: Object, default: () => ({ create: false, delete: false }) },
})

/* ─────────────────────────────────────────────────────────
 * Composables / Stores
 * ───────────────────────────────────────────────────────── */
const store = useKanbanStore()
const { authUser } = useAuth()

/* ─────────────────────────────────────────────────────────
 * UI state
 * ───────────────────────────────────────────────────────── */
const sidebarOpen = ref(false)
const showCreate  = ref(false)
const showEdit    = ref(false)
const editingId   = ref(null)

/* ─────────────────────────────────────────────────────────
 * Computed
 * ───────────────────────────────────────────────────────── */
const canInternal = computed(() =>
    ['admin', 'agent'].includes(authUser.value?.role)
)

const sortedStatuses = computed(() =>
    Array.isArray(props.statuses)
        ? [...props.statuses].sort((a, b) => a.order - b.order)
        : []
)

// ── Opciones SelectInput ──────────────────────────────────────────────────────
const priorityOptions = computed(() => [
    { id: '', name: 'Todas' },
    ...props.priorities.map(p => ({ id: p.id, name: p.name })),
])
const projectOptions = computed(() => [
    { id: '', name: 'Todos' },
    ...props.projects.map(p => ({ id: p.id, name: p.name })),
])
const categoryOptions = computed(() => [
    { id: '', name: 'Todas' },
    ...props.categories.map(c => ({ id: c.id, name: c.name })),
])
const typeOptions = computed(() => [
    { id: '', name: 'Todos' },
    ...props.types.map(t => ({ id: t.id, name: t.name })),
])
const agentOptions = computed(() => [
    { id: '', name: 'Todos' },
    ...props.agents.map(a => ({ id: a.id, name: a.name })),
])

/* ─────────────────────────────────────────────────────────
 * Actions
 * ───────────────────────────────────────────────────────── */
// store.applyFilter — un solo método reemplaza el patrón
//    store.setFilter(key, value) + store.fetchTickets() duplicado antes
//    en Index.vue Y en KanbanFilterBar. El store garantiza que ambos pasos
//    siempre ocurran juntos.
const applyFilter = (key, value) => store.applyFilter(key, value === '' ? null : value)

// store.clearFilters ya incluye el fetchTickets internamente.
//    clearAll solo gestiona la UI: cierra el sidebar.
const clearAll = async () => {
    sidebarOpen.value = false
    await store.clearFilters()
}

const openEdit = (ticketId) => {
    editingId.value = ticketId
    showEdit.value  = true
}

const onTicketCreated = () => store.fetchTickets()

/* ─────────────────────────────────────────────────────────
 * Lifecycle
 * ───────────────────────────────────────────────────────── */
onMounted(() => store.fetchTickets())
</script>

<template>

    <Head title="Kanban — Helpdesk" />
    <AuthenticatedLayout>
        <div class="flex flex-col h-full gap-5">

            <!-- ── Page header ─────────────────────────────────────────────── -->
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        Tablero Kanban
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Gestión visual de tickets de soporte
                    </p>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                                bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <span class="material-symbols-outlined text-slate-500 text-sm">confirmation_number</span>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                            {{ store.openCount }} tickets
                        </span>
                    </div>

                    <div v-if="store.urgentCount > 0" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                               bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
                        <span class="material-symbols-outlined text-danger text-sm">priority_high</span>
                        <span class="text-xs font-bold text-danger">{{ store.urgentCount }} alta prioridad</span>
                    </div>

                    <PrimaryButton v-if="can.create" icon="add" @click="showCreate = true">
                        Nuevo Ticket
                    </PrimaryButton>
                </div>
            </div>

            <!-- ── Filtros: botón + chips ───────────────────────────────────── -->
            <KanbanFilterBar
                :priorities="priorities"
                :agents="agents"
                :projects="projects"
                :categories="categories"
                :types="types"
                @open-sidebar="sidebarOpen = true"
            />

            <!-- ── Loading skeleton ────────────────────────────────────────── -->
            <div v-if="store.loading" class="flex gap-5 overflow-hidden">
                <div v-for="i in sortedStatuses.length || 4" :key="i"
                    class="w-72 shrink-0 rounded-2xl bg-slate-100 dark:bg-slate-800 h-64 animate-pulse" />
            </div>

            <!-- ── Kanban board ─────────────────────────────────────────────── -->
            <div v-else class="flex gap-5 overflow-x-auto pb-4 pt-1 pr-2 custom-scrollbar"
                style="min-height: calc(100vh - 15rem)">
                <KanbanColumn
                    v-for="status in sortedStatuses"
                    :key="status.id"
                    :status="status"
                    @edit-ticket="openEdit"
                />

                <div v-if="sortedStatuses.length === 0"
                    class="flex-1 flex flex-col items-center justify-center text-slate-400 gap-3">
                    <span class="material-symbols-outlined text-5xl">view_kanban</span>
                    <p class="text-sm">No hay estados de ticket configurados</p>
                </div>
            </div>
        </div>

        <!-- ── SidebarFilter ───────────────────────────────────────────────── -->
        <SidebarFilter
            v-model:open="sidebarOpen"
            :active-count="store.activeFilterCount"
            title="Filtrar tickets"
            @clear="clearAll"
        >
            <div class="space-y-5 pt-4">

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Búsqueda rápida</label>
                    <TextInput
                        :model-value="store.filters.search ?? ''"
                        placeholder="Título o número..."
                        icon="search"
                        @update:model-value="v => applyFilter('search', v)"
                    />
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Prioridad</label>
                    <SelectInput
                        :model-value="store.filters.priority_id ?? ''"
                        :options="priorityOptions"
                        @update:model-value="v => applyFilter('priority_id', v ? Number(v) : null)"
                    />
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Proyecto</label>
                    <SelectInput
                        :model-value="store.filters.project_id ?? ''"
                        :options="projectOptions"
                        @update:model-value="v => applyFilter('project_id', v ? Number(v) : null)"
                    />
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Categoría</label>
                    <SelectInput
                        :model-value="store.filters.category_id ?? ''"
                        :options="categoryOptions"
                        @update:model-value="v => applyFilter('category_id', v ? Number(v) : null)"
                    />
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo</label>
                    <SelectInput
                        :model-value="store.filters.type_id ?? ''"
                        :options="typeOptions"
                        @update:model-value="v => applyFilter('type_id', v ? Number(v) : null)"
                    />
                </div>

                <div v-if="canInternal">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Asignado a</label>
                    <SelectInput
                        :model-value="store.filters.assigned_to ?? ''"
                        :options="agentOptions"
                        @update:model-value="v => applyFilter('assigned_to', v ? Number(v) : null)"
                    />
                </div>
            </div>
        </SidebarFilter>

        <!-- ── Modales ─────────────────────────────────────────────────────── -->
        <CreateTicketModal
            :show="showCreate"
            :statuses="statuses"
            :priorities="priorities"
            :types="types"
            :categories="categories"
            :projects="projects"
            :helpdesks="helpdesks"
            @close="showCreate = false"
            @created="onTicketCreated"
        />

        <EditTicketModal
            :show="showEdit"
            :ticket-id="editingId"
            :statuses="statuses"
            :priorities="priorities"
            :types="types"
            :categories="categories"
            :projects="projects"
            :helpdesks="helpdesks"
            :can-internal="canInternal"
            :can-delete="can.delete"
            @close="showEdit = false"
            @updated="store.fetchTickets()"
            @deleted="store.fetchTickets()"
        />

    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-slate-100 dark:bg-slate-800 rounded-full;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-300 dark:bg-slate-600 rounded-full;
}
</style>
