<script setup>
// resources/js/Pages/Companies/Index.vue
import { computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { useFilters } from '@/shared/composables/useFilters'
import { useDatatable } from '@/shared/composables/useDatatable'
import { useAuth } from '@/shared/composables/useAuth' // <-- importado
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ActionMenu from '@/shared/components/ActionMenu.vue'
import SidebarFilter from '@/shared/components/SidebarFilter.vue'
import Datatable from '@/shared/components/Datatable.vue'
import Modal from '@/shared/components/Modal.vue'
import { useModal } from '@/shared/composables/useModal'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'
import FilterButton from '@/shared/components/buttons/FilterButton.vue'

const props = defineProps({
    companies: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    can: { type: Object, default: () => ({}) },
})

// Usar useAuth para obtener el usuario y su rol
const { isSuperAdmin } = useAuth()

// ─── Lógica de Filtros ──────────────────────────────────────────────
// En lugar de desestructurar, asigna el resultado completo a filterManager
const filterManager = useFilters('companies.index', {
    search: props.filters.search ?? '',
    is_active: props.filters.is_active ?? '',
})



// ─── Datatable ──────────────────────────────────────────────────────
const dt = useDatatable('companies.index', () => props.companies, filterManager, {
    emptyText: 'No hay compañías que coincidan.',
    emptyIcon: 'business_center',
})

// ─── Lógica de Modal ────────────────────────────────────────────────
const deactivateModal = useModal()
const deactivateForm = useForm({})

const executeDeactivation = () => {
    if (!deactivateModal.item) return

    deactivateForm.delete(route('companies.destroy', deactivateModal.item.id), {
        preserveScroll: true,
        onSuccess: () => deactivateModal.close(),
    })
}

// ─── Configuración Datatable ───────────────────────────────────────
const columns = [
    { key: 'company', label: 'Compañía' },
    { key: 'slug', label: 'Slug', class: 'hidden md:table-cell' },
    { key: 'users_count', label: 'Usuarios', class: 'hidden lg:table-cell' },
    { key: 'status', label: 'Estado', align: 'center' },
]

// ─── Acciones por fila ─────────────────────────────────────────────
const rowActions = (company) => {
    const actions = []
    if (props.can.update) {
        actions.push({
            label: 'Editar',
            icon: 'edit',
            handler: () => router.visit(route('companies.edit', company.id)),
        })
    }
    if (props.can.deactivate && company.is_active) {
        actions.push({ separator: true })
        actions.push({
            label: 'Desactivar',
            icon: 'block',
            variant: 'danger',
            handler: () => deactivateModal.open(company),
        })
    }
    return actions
}
</script>

<template>
    <Head title="Compañías" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-5">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight text-balance">Compañías</h1>
                    <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 mt-0.5 uppercase tracking-widest">
                        {{ companies.total }} registros encontrados
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <FilterButton
                        v-if="isSuperAdmin"
                        :active-count="filterManager.activeCount"
                        @click="filterManager.isOpen = true"
                    />
                    <PrimaryButton v-if="can.create" @click="router.visit(route('companies.create'))">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Nueva compañía
                    </PrimaryButton>
                </div>
            </div>

            <!-- Chips de filtros activos -->
            <div v-if="isSuperAdmin && filterManager.activeCount > 0" class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Filtros activos:</span>

                <span v-if="filterManager.filters.search" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">search</span>
                    "{{ filterManager.filters.search }}"
                    <button @click="filterManager.filters.search = ''" class="hover:text-brand-dark transition-colors">
                        <span class="material-symbols-outlined text-[14px] font-black">close</span>
                    </button>
                </span>

                <span v-if="filterManager.filters.is_active !== ''" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">toggle_on</span>
                    {{ filterManager.filters.is_active === '1' ? 'Activas' : 'Inactivas' }}
                    <button @click="filterManager.filters.is_active = ''" class="hover:text-brand-dark transition-colors">
                        <span class="material-symbols-outlined text-[14px] font-black">close</span>
                    </button>
                </span>
            </div>

            <!-- Datatable unificado con useDatatable -->
            <Datatable v-bind="dt.bind" :columns="columns" @page-change="dt.changePage">
                <template #cell-company="{ row }">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl overflow-hidden shrink-0 bg-brand/10 flex items-center justify-center border border-brand/20">
                            <img v-if="row.logo" :src="`/storage/${row.logo}`" class="h-full w-full object-cover" />
                            <span v-else class="text-sm font-black text-brand">{{ row.name.charAt(0).toUpperCase() }}</span>
                        </div>
                        <p class="font-bold text-slate-900 dark:text-white truncate">{{ row.name }}</p>
                    </div>
                </template>

                <template #cell-status="{ row }">
                    <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border border-current/10 transition-all"
                            :class="row.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20' : 'bg-slate-100 text-slate-500 opacity-60'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="row.is_active ? 'bg-emerald-500' : 'bg-slate-400'" />
                            {{ row.is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                </template>

                <template #actions="{ row }">
                    <ActionMenu v-if="rowActions(row).length" :actions="rowActions(row)" />
                </template>
            </Datatable>
        </div>

        <SidebarFilter v-if="isSuperAdmin" v-model:open="filterManager.isOpen"
            :active-count="filterManager.activeCount" title="Filtrar compañías" @clear="filterManager.clear()">
            <div class="space-y-6 pt-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nombre de Compañía</label>
                    <input v-model="filterManager.filters.search" type="text" placeholder="Buscar..."
                        class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 focus:outline-none focus:border-brand transition-all text-slate-900 dark:text-white placeholder:text-slate-400" />
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 text-balance">Estado</label>
                    <select v-model="filterManager.filters.is_active"
                        class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 outline-none focus:border-brand transition-all text-slate-700 dark:text-slate-300">
                        <option value="">Todas las compañías</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                </div>
            </div>
        </SidebarFilter>

        <Modal :show="deactivateModal.isOpen" title="Confirmar Acción" variant="danger" size="sm" @close="deactivateModal.close()">
            <template #icon>
                <span class="material-symbols-outlined text-danger">warning</span>
            </template>
            <div class="space-y-4">
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed text-balance">
                    ¿Estás seguro de que deseas desactivar a
                    <span class="font-black text-slate-900 dark:text-white">"{{ deactivateModal.item?.name }}"</span>?
                </p>
                <div class="p-3 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-xl text-[10px] font-black text-red-700 dark:text-red-400 uppercase tracking-widest leading-relaxed">
                    Aviso: Se suspenderá el acceso a usuarios y proyectos asociados.
                </div>
            </div>
            <template #footer>
                <SecondaryButton @click="deactivateModal.close()">
                    Cancelar
                </SecondaryButton>
                <PrimaryButton variant="danger" @click="executeDeactivation" :loading="deactivateForm.processing">
                    Confirmar
                </PrimaryButton>
            </template>
        </Modal>
    </AuthenticatedLayout>
</template>
