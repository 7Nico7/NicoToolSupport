
<script setup>
// resources/js/Pages/Categories/Index.vue
import { computed } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ActionMenu      from '@/shared/components/ActionMenu.vue'
import SidebarFilter   from '@/shared/components/SidebarFilter.vue'
import TextInput       from '@/shared/components/TextInput.vue'
import SelectInput     from '@/shared/components/SelectInput.vue'
import Datatable       from '@/shared/components/Datatable.vue'
import Modal           from '@/shared/components/Modal.vue'
import PrimaryButton   from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'
import FilterButton    from '@/shared/components/buttons/FilterButton.vue'
import { useFilters }  from '@/shared/composables/useFilters'
import { useAuth }     from '@/shared/composables/useAuth'
import { useDatatable }from '@/shared/composables/useDatatable'
import { useModal }    from '@/shared/composables/useModal'

const props = defineProps({
    categories: { type: Object, required: true },
    filters:    { type: Object, default: () => ({}) },
    companies:  { type: Array,  default: () => [] },
    can:        { type: Object, default: () => ({}) },
})

const { isSuperAdmin } = useAuth()

// ── Filtros ───────────────────────────────────────────────────────────────────
const filterManager = useFilters('categories.index', {
    search:     props.filters.search     ?? '',
    company_id: props.filters.company_id ?? '',
})

// ── Datatable ─────────────────────────────────────────────────────────────────
const dt = useDatatable('categories.index', () => props.categories, filterManager, {
    emptyText: 'No se encontraron categorías',
    emptyIcon: 'category',
})

// ── Opciones SelectInput ──────────────────────────────────────────────────────
const companyOptions = computed(() => [
    { id: '', name: 'Todas las compañías' },
    ...props.companies.map(c => ({ id: c.id, name: c.name })),
])

// ── Columnas ──────────────────────────────────────────────────────────────────
const columns = computed(() => {
    const cols = [
        { key: 'name',          label: 'Categoría', sortable: true },
        { key: 'tickets_count', label: 'Tickets',   align: 'center', class: 'w-24' },
    ]
    if (isSuperAdmin.value) {
        cols.splice(1, 0, { key: 'company', label: 'Compañía', class: 'hidden lg:table-cell' })
    }
    return cols
})

// ── Modal eliminación ─────────────────────────────────────────────────────────
const deleteModal = useModal()
const deleteForm  = useForm({})

const executeDelete = () => {
    if (!deleteModal.item) return
    deleteForm.delete(route('categories.destroy', deleteModal.item.id), {
        preserveScroll: true,
        onSuccess: () => deleteModal.close(),
    })
}

// ── Acciones por fila ─────────────────────────────────────────────────────────
const rowActions = (category) => {
    const actions = []
    if (props.can.update) {
        actions.push({
            label:   'Editar',
            icon:    'edit',
            handler: () => router.visit(route('categories.edit', category.id)),
        })
    }
    if (props.can.delete) {
        actions.push({ separator: true })
        actions.push({
            label:   'Eliminar',
            icon:    'delete',
            variant: 'danger',
            handler: () => deleteModal.open(category),
        })
    }
    return actions
}
</script>

<template>
    <Head title="Categorías" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-5">

            <!-- ── Page header ────────────────────────────────────────────── -->
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Categorías</h1>
                    <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 mt-0.5 uppercase tracking-widest">
                        {{ categories.total }} registros encontrados
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <FilterButton :active-count="filterManager.activeCount" @click="filterManager.isOpen = true" />
                    <Link v-if="can.create" :href="route('categories.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest
                               bg-brand text-white shadow-lg shadow-brand/20 hover:scale-[1.02] transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Nueva categoría
                    </Link>
                </div>
            </div>

            <!-- ── Chips filtros activos ───────────────────────────────────── -->
            <div v-if="filterManager.activeCount > 0" class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                    Filtros activos:
                </span>
                <span v-if="filterManager.filters.search"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">search</span>
                    "{{ filterManager.filters.search }}"
                    <button @click="filterManager.filters.search = ''">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </span>
                <span v-if="filterManager.filters.company_id"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">business</span>
                    {{ companyOptions.find(c => c.id == filterManager.filters.company_id)?.name ?? filterManager.filters.company_id }}
                    <button @click="filterManager.filters.company_id = ''">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </span>
            </div>

            <!-- ── Datatable ───────────────────────────────────────────────── -->
            <Datatable v-bind="dt.bind" :columns="columns" @page-change="dt.changePage">

                <template #cell-name="{ row }">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0 border border-brand/10">
                            <span class="material-symbols-outlined text-[16px]">category</span>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-white">{{ row.name }}</span>
                    </div>
                </template>

                <template #cell-company="{ row }">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        {{ row.company?.name ?? '—' }}
                    </span>
                </template>

                <template #cell-tickets_count="{ row }">
                    <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg
                                     bg-slate-50 dark:bg-slate-800/50 text-xs font-bold
                                     text-slate-600 dark:text-slate-400
                                     border border-slate-100 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[14px]">confirmation_number</span>
                            {{ row.tickets_count ?? 0 }}
                        </span>
                    </div>
                </template>

                <template #actions="{ row }">
                    <ActionMenu v-if="rowActions(row).length" :actions="rowActions(row)" />
                </template>

            </Datatable>
        </div>

        <!-- ── SidebarFilter ──────────────────────────────────────────────── -->
        <SidebarFilter
            v-model:open="filterManager.isOpen"
            :active-count="filterManager.activeCount"
            title="Filtrar categorías"
            @clear="filterManager.clear()"
        >
            <div class="space-y-6 pt-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        Búsqueda rápida
                    </label>
                    <TextInput v-model="filterManager.filters.search" icon="search" placeholder="Nombre de la categoría..." />
                </div>
                <div v-if="isSuperAdmin">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        Compañía
                    </label>
                    <SelectInput v-model="filterManager.filters.company_id" :options="companyOptions" />
                </div>
            </div>
        </SidebarFilter>

        <!-- ── Modal eliminación ──────────────────────────────────────────── -->
        <Modal :show="deleteModal.isOpen" title="Eliminar categoría" variant="danger" size="sm"
            @close="deleteModal.close()">
            <template #icon>
                <span class="material-symbols-outlined text-danger">warning</span>
            </template>
            <div class="space-y-4">
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    ¿Confirmas que deseas eliminar la categoría
                    <span class="font-black text-slate-900 dark:text-white">"{{ deleteModal.item?.name }}"</span>?
                </p>
                <div class="p-3 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30
                            rounded-xl text-[10px] font-black text-red-700 dark:text-red-400 uppercase tracking-widest">
                    Esta acción no se puede deshacer. Solo es posible si no hay tickets asociados.
                </div>
            </div>
            <template #footer>
                <SecondaryButton @click="deleteModal.close()">Cancelar</SecondaryButton>
                <PrimaryButton variant="danger" @click="executeDelete" :loading="deleteForm.processing">
                    Eliminar
                </PrimaryButton>
            </template>
        </Modal>

    </AuthenticatedLayout>
</template>
