<script setup>
// resources/js/Pages/Projects/Index.vue
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SidebarFilter from '@/shared/components/SidebarFilter.vue'
import TextInput from '@/shared/components/TextInput.vue'
import SelectInput from '@/shared/components/SelectInput.vue'
import Datatable from '@/shared/components/Datatable.vue'
import ActionMenu from '@/shared/components/ActionMenu.vue'
import Modal from '@/shared/components/Modal.vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'
import FilterButton from '@/shared/components/buttons/FilterButton.vue'
import { useFilters } from '@/shared/composables/useFilters'
import { useDatatable } from '@/shared/composables/useDatatable'
import { useModal } from '@/shared/composables/useModal'

const props = defineProps({
    projects: { type: Object, required: true },
    filters:  { type: Object, default: () => ({}) },
    can:      { type: Object, default: () => ({}) },
})

// ── Filtros ───────────────────────────────────────────────────────────────────
const filterManager = useFilters('projects.index', {
    search: props.filters.search ?? '',
    is_active: props.filters.is_active ?? '',
})
// ── Datatable ─────────────────────────────────────────────────────────────────
const dt = useDatatable('projects.index', () => props.projects, filterManager, {
    emptyText: 'No hay proyectos que coincidan.',
    emptyIcon: 'folder_open',
})

// ── Opciones SelectInput ──────────────────────────────────────────────────────
const statusOptions = [
    { id: '', name: 'Todos los estados' },
    { id: '1', name: 'Activos' },
    { id: '0', name: 'Inactivos' },
]

// ── Columnas ──────────────────────────────────────────────────────────────────
const columns = [
    { key: 'project', label: 'Proyecto' },
    { key: 'company', label: 'Compañía', class: 'hidden lg:table-cell' },
    { key: 'status',  label: 'Estado',   align: 'center' },
]

// ── Modal desactivación ───────────────────────────────────────────────────────
const deactivateModal = useModal()
const deactivateForm  = useForm({})

const executeDeactivation = () => {
    if (!deactivateModal.item) return
    deactivateForm.delete(route('projects.destroy', deactivateModal.item.id), {
        preserveScroll: true,
        onSuccess: () => deactivateModal.close(),
    })
}

// ── Acciones por fila ─────────────────────────────────────────────────────────
const rowActions = (project) => {
    const actions = []
    if (props.can.update) {
        actions.push({
            label:   'Editar',
            icon:    'edit',
            handler: () => router.visit(route('projects.edit', project.id)),
        })
    }
    if (props.can.deactivate && project.is_active) {
        actions.push({ separator: true })
        actions.push({
            label:   'Desactivar',
            icon:    'block',
            variant: 'danger',
            handler: () => deactivateModal.open(project),
        })
    }
    return actions
}
</script>

<template>

    <Head title="Proyectos" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-5">

            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Proyectos</h1>
                    <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 mt-0.5 uppercase tracking-widest">
                        {{ projects.total }} registros encontrados
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <FilterButton :active-count="filterManager.activeCount" @click="filterManager.isOpen = true" />
                    <Link v-if="can.create" :href="route('projects.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest bg-brand text-white shadow-lg shadow-brand/20 hover:scale-[1.02] transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Nuevo proyecto
                    </Link>
                </div>
            </div>

            <!-- Chips filtros activos -->
            <div v-if="filterManager.activeCount > 0" class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Filtros activos:</span>
                <span v-if="filterManager.filters.search"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">search</span>
                    "{{ filterManager.filters.search }}"
                    <button @click="filterManager.filters.search = ''"><span class="material-symbols-outlined text-[14px]">close</span></button>
                </span>
                <span v-if="filterManager.filters.is_active !== ''"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">toggle_on</span>
                    {{ filterManager.filters.is_active === '1' ? 'Activos' : 'Inactivos' }}
                    <button @click="filterManager.filters.is_active = ''"><span class="material-symbols-outlined text-[14px]">close</span></button>
                </span>
            </div>

            <!-- Patrón único: v-bind="dt.bind" + :columns + @page-change="dt.changePage" -->
            <Datatable v-bind="dt.bind" :columns="columns" @page-change="dt.changePage">

                <template #cell-project="{ row }">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl shrink-0 bg-brand/10 flex items-center justify-center font-black text-brand text-sm border border-brand/20">
                            {{ row.name.charAt(0).toUpperCase() }}
                        </div>
                        <p class="font-bold text-slate-900 dark:text-white truncate">{{ row.name }}</p>
                    </div>
                </template>

                <template #cell-company="{ row }">
                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400 text-[11px] font-bold uppercase tracking-tight">
                        <span class="material-symbols-outlined text-[16px]">business</span>
                        <span>{{ row.company?.name ?? '—' }}</span>
                    </div>
                </template>

                <template #cell-status="{ row }">
                    <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border border-current/10 transition-all"
                            :class="row.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20' : 'bg-slate-100 text-slate-500 opacity-60'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="row.is_active ? 'bg-emerald-500' : 'bg-slate-400'" />
                            {{ row.is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </template>

                <template #actions="{ row }">
                    <ActionMenu v-if="rowActions(row).length" :actions="rowActions(row)" />
                </template>

            </Datatable>
        </div>

        <SidebarFilter v-model:open="filterManager.isOpen" :active-count="filterManager.activeCount"
            title="Filtrar proyectos" @clear="filterManager.clear()">
            <div class="space-y-6 pt-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Proyecto / Descripción</label>
                    <TextInput v-model="filterManager.filters.search" icon="search" placeholder="Buscar..." />
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Estado del proyecto</label>
                    <SelectInput v-model="filterManager.filters.is_active" :options="statusOptions" />
                </div>
            </div>
        </SidebarFilter>

        <Modal :show="deactivateModal.isOpen" title="Confirmar Acción" variant="danger" size="sm"
            @close="deactivateModal.close()">
            <template #icon>
                <span class="material-symbols-outlined text-danger">warning</span>
            </template>
            <div class="space-y-4">
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed text-balance">
                    ¿Estás seguro de que deseas desactivar el proyecto
                    <span class="font-black text-slate-900 dark:text-white">"{{ deactivateModal.item?.name }}"</span>?
                </p>
            </div>
            <template #footer>
                <SecondaryButton @click="deactivateModal.close()">Cancelar</SecondaryButton>
                <PrimaryButton variant="danger" @click="executeDeactivation" :loading="deactivateForm.processing">
                    Confirmar
                </PrimaryButton>
            </template>
        </Modal>

    </AuthenticatedLayout>
</template>
