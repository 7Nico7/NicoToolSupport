<script setup>
// resources/js/Pages/Gantt/Index.vue
import { onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GanttChart from '@/features/Gantt/components/GanttChart.vue';
import SidebarFilter from '@/shared/components/SidebarFilter.vue';
import SelectInput from '@/shared/components/SelectInput.vue';
import TextInput from '@/shared/components/TextInput.vue';
import FilterButton from '@/shared/components/buttons/FilterButton.vue';
import { useGanttStore } from '@/features/Gantt/stores/ganttStore';
import { useAuth } from '@/shared/composables/useAuth';
import { useGanttFilters } from '@/features/Gantt/composables/useGanttFilters';

// ── Props (Inertia) ───────────────────────────────────────────────────────────
const props = defineProps({
    statuses: { type: Array, default: () => [] },
    priorities: { type: Array, default: () => [] },
    types: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    agents: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    tickets: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    meta: { type: Object, default: () => ({ total: 0, requiresCompanyFilter: false }) },
});

// ── Store + Auth ──────────────────────────────────────────────────────────────
const store = useGanttStore();
const { isSuperAdmin } = useAuth();

// ── Filtros ───────────────────────────────────────────────────────────────────
// Toda la lógica de filtros, ventana temporal y navegación vive en el composable.
// Index.vue solo declara qué datos necesita — no implementa nada de filtros.
const gf = useGanttFilters(props.filters, store, props);

// ── Opciones para SelectInput ─────────────────────────────────────────────────
// Se mantienen aquí porque dependen de props — son datos, no lógica de filtros.
import { computed } from 'vue';
const companyOptions = computed(() => [{ id: '', name: 'Todas las compañías' }, ...props.companies.map(c => ({ id: c.id, name: c.name }))]);
const projectOptions = computed(() => [{ id: '', name: 'Todos' }, ...props.projects.map(p => ({ id: p.id, name: p.name }))]);
const statusOptions = computed(() => [{ id: '', name: 'Todos' }, ...props.statuses.map(s => ({ id: s.id, name: s.name }))]);
const priorityOptions = computed(() => [{ id: '', name: 'Todas' }, ...props.priorities.map(p => ({ id: p.id, name: p.name }))]);
const typeOptions = computed(() => [{ id: '', name: 'Todos' }, ...props.types.map(t => ({ id: t.id, name: t.name }))]);
const categoryOptions = computed(() => [{ id: '', name: 'Todas' }, ...props.categories.map(c => ({ id: c.id, name: c.name }))]);
const agentOptions = computed(() => [{ id: '', name: 'Todos' }, ...props.agents.map(a => ({ id: a.id, name: a.name }))]);

const selectedCompanyName = computed(() => {
    if (!isSuperAdmin.value || !gf.filters.company_id) return null;
    return props.companies.find(c => c.id == gf.filters.company_id)?.name ?? null;
});

// Claves del sidebar visibles como chips
const SIDEBAR_KEYS = ['status_id', 'priority_id', 'type_id', 'category_id', 'project_id', 'assigned_to', 'company_id'];

// ── Hydrate store ─────────────────────────────────────────────────────────────
onMounted(() => {
    store.setCompanies(props.companies);
    store.setTickets(props.tickets);
});
watch(() => props.tickets, (v) => store.setTickets(v));
watch(() => props.companies, (v) => store.setCompanies(v));

// ── Edit hook ─────────────────────────────────────────────────────────────────
const handleEditTicket = (ticketId) => { console.info('[Gantt] Edit ticket:', ticketId); };
</script>

<template>

    <Head title="Diagrama de Gantt" />

    <AuthenticatedLayout>
        <div class="flex flex-col h-full gap-4">

            <!-- ── Page header ─────────────────────────────────────────────── -->
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Diagrama de Gantt</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Flujo de trabajo y línea de tiempo de tickets
                    </p>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <div v-if="store.overdueCount > 0"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
                        <span class="material-symbols-outlined text-danger text-sm">warning</span>
                        <span class="text-xs font-bold text-danger">{{ store.overdueCount }} vencidos</span>
                    </div>
                    <div v-if="isSuperAdmin && selectedCompanyName"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand/5 border border-brand/20">
                        <span class="material-symbols-outlined text-brand text-sm">business</span>
                        <span class="text-xs font-bold text-brand">{{ selectedCompanyName }}</span>
                    </div>
                </div>
            </div>

            <!-- ── Barra de navegación + controles ───────────────────────────── -->
            <div
                class="flex items-center gap-2 flex-wrap px-3 py-2 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">

                <!-- Prev / Next -->
                <button type="button" @click="gf.navigate('prev')" title="Período anterior"
                    class="flex items-center justify-center w-8 h-8 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-all">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </button>

                <div class="min-w-[150px] text-center">
                    <span class="text-sm font-black text-slate-800 dark:text-white capitalize">{{ gf.windowLabel
                        }}</span>
                </div>

                <button type="button" @click="gf.navigate('next')" title="Período siguiente"
                    class="flex items-center justify-center w-8 h-8 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-all">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </button>

                <button type="button" @click="gf.goToday()"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-brand hover:text-brand bg-white dark:bg-slate-800 transition-all">
                    Hoy
                </button>

                <!-- Contador -->
                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <span class="material-symbols-outlined text-[16px]">confirmation_number</span>
                    <span class="font-semibold">{{ store.totalTickets }} tickets</span>
                    <span v-if="meta.total > store.totalTickets" class="text-slate-400">de {{ meta.total }}</span>
                </div>

                <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 mx-1" />

                <!-- Zoom -->
                <div class="flex items-center gap-1.5">
                    <span
                        class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Vista</span>
                    <div class="flex rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
                        <button v-for="opt in [{ value: 'week', label: 'Semana' }, { value: 'month', label: 'Mes' }]"
                            :key="opt.value" type="button" @click="gf.changeZoom(opt.value)"
                            class="px-3 py-1.5 text-xs font-bold transition-all"
                            :class="store.zoom === opt.value
                                ? 'bg-brand text-white'
                                : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">{{ opt.label }}</button>
                    </div>
                </div>

                <div class="flex-auto" />
                <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 mx-1" />

                <!-- Filtros -->
                <FilterButton :active-count="gf.activeCount" @click="gf.isOpen = true" />

                <!-- Chips filtros activos del sidebar -->
                <template v-for="key in SIDEBAR_KEYS" :key="key">
                    <span v-if="gf.filters[key]"
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-brand/10 text-brand">
                        {{ gf.resolveLabel(key, gf.filters[key]) }}
                        <button type="button" @click="gf.updateFilter(key, '')"
                            class="hover:opacity-60 transition-opacity leading-none">
                            <span class="material-symbols-outlined text-[13px]">close</span>
                        </button>
                    </span>
                </template>
            </div>

            <!-- ── Gantt Chart ──────────────────────────────────────────────── -->
            <div class="flex-1 min-h-0" style="min-height: calc(100vh - 22rem)">
                <GanttChart :on-edit-ticket="handleEditTicket" :requires-company-filter="meta.requiresCompanyFilter" />
            </div>

        </div>

        <!-- ── SidebarFilter ───────────────────────────────────────────────── -->
        <SidebarFilter v-model:open="gf.isOpen" :active-count="gf.activeCount" title="Filtrar tickets"
            @clear="gf.clearFilters()">

            <div class="space-y-5 pt-4">

                <!-- Ventana de fechas -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Vence
                        desde</label>
                    <TextInput type="date" :model-value="gf.filters.due_after"
                        @update:model-value="v => gf.updateFilter('due_after', v)" />
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Vence
                        hasta</label>
                    <TextInput type="date" :model-value="gf.filters.due_before"
                        @update:model-value="v => gf.updateFilter('due_before', v)" />
                </div>

                <!-- Filtros de sidebar -->
                <div v-if="isSuperAdmin && companies.length">
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Compañía</label>
                    <SelectInput :model-value="gf.filters.company_id" :options="companyOptions"
                        @update:model-value="v => gf.updateFilter('company_id', v)" />
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Proyecto</label>
                    <SelectInput :model-value="gf.filters.project_id" :options="projectOptions"
                        @update:model-value="v => gf.updateFilter('project_id', v)" />
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Estado</label>
                    <SelectInput :model-value="gf.filters.status_id" :options="statusOptions"
                        @update:model-value="v => gf.updateFilter('status_id', v)" />
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Prioridad</label>
                    <SelectInput :model-value="gf.filters.priority_id" :options="priorityOptions"
                        @update:model-value="v => gf.updateFilter('priority_id', v)" />
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo</label>
                    <SelectInput :model-value="gf.filters.type_id" :options="typeOptions"
                        @update:model-value="v => gf.updateFilter('type_id', v)" />
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Categoría</label>
                    <SelectInput :model-value="gf.filters.category_id" :options="categoryOptions"
                        @update:model-value="v => gf.updateFilter('category_id', v)" />
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Asignado
                        a</label>
                    <SelectInput :model-value="gf.filters.assigned_to" :options="agentOptions"
                        @update:model-value="v => gf.updateFilter('assigned_to', v)" />
                </div>
            </div>
        </SidebarFilter>

    </AuthenticatedLayout>
</template>
