<script setup>
// resources/js/Pages/Users/Index.vue
import { computed } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ActionMenu from '@/shared/components/ActionMenu.vue'
import SidebarFilter from '@/shared/components/SidebarFilter.vue'
import TextInput from '@/shared/components/TextInput.vue'
import SelectInput from '@/shared/components/SelectInput.vue'
import Datatable from '@/shared/components/Datatable.vue'
import Modal from '@/shared/components/Modal.vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'
import FilterButton from '@/shared/components/buttons/FilterButton.vue'
import { useFilters } from '@/shared/composables/useFilters'
import { useAuth } from '@/shared/composables/useAuth'
import { useDatatable } from '@/shared/composables/useDatatable'
import { useModal } from '@/shared/composables/useModal'
import { ROLE_CONFIG, roleOptions } from '@/shared/constants/roles'

const props = defineProps({
    users: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    companies: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
})

const { authUser, isSuperAdmin } = useAuth()

// ── Filtros ───────────────────────────────────────────────────────────────────
const filterManager = useFilters('users.index', {
    search: props.filters.search ?? '',
    role: props.filters.role ?? '',
    is_active: props.filters.is_active ?? '',
    company_id: props.filters.company_id ?? '',
})

// ── Datatable ─────────────────────────────────────────────────────────────────
const dt = useDatatable('users.index', () => props.users, filterManager, {
    emptyText: 'No se encontraron usuarios',
    emptyIcon: 'group_off',
})

// ── Opciones SelectInput ──────────────────────────────────────────────────────
const statusOptions = [
    { id: '', name: 'Todos los estados' },
    { id: '1', name: 'Activos' },
    { id: '0', name: 'Inactivos' },
]
const roleFilterOptions = [{ id: '', name: 'Todos los roles' }, ...roleOptions]
const companyOptions = computed(() => [
    { id: '', name: 'Todas las compañías' },
    ...props.companies.map(c => ({ id: c.id, name: c.name })),
])

// ── Columnas ──────────────────────────────────────────────────────────────────
const columns = computed(() => {
    const cols = [
        { key: 'name', label: 'Usuario', sortable: true },
        { key: 'email', label: 'Email', sortable: true, class: 'hidden sm:table-cell' },
    ]
    if (isSuperAdmin.value) {
        cols.push({ key: 'company', label: 'Compañía', class: 'hidden lg:table-cell' })
    }
    cols.push({ key: 'role', label: 'Rol', align: 'left', class: 'hidden md:table-cell' })
    cols.push({ key: 'is_active', label: 'Estado', align: 'center' })
    return cols
})

// ── Modal desactivación ───────────────────────────────────────────────────────
const deactivateModal = useModal()
const deactivateForm = useForm({})

const executeDeactivation = () => {
    if (!deactivateModal.item) return
    deactivateForm.delete(route('users.destroy', deactivateModal.item.id), {
        preserveScroll: true,
        onSuccess: () => deactivateModal.close(),
    })
}

// ── Acciones por fila ─────────────────────────────────────────────────────────
const rowActions = (user) => {
    const actions = []
    if (props.can.update) {
        actions.push({
            label: 'Editar',
            icon: 'edit',
            handler: () => router.visit(route('users.edit', user.id)),
        })
    }
    if (props.can.deactivate && user.is_active && user.id !== authUser.value.id) {
        actions.push({ separator: true })
        actions.push({
            label: 'Desactivar',
            icon: 'person_off',
            variant: 'danger',
            handler: () => deactivateModal.open(user),
        })
    }
    return actions
}
</script>

<template>

    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-5">

            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Usuarios</h1>
                    <p
                        class="text-[11px] font-black text-slate-500 dark:text-slate-400 mt-0.5 uppercase tracking-widest">
                        {{ users.total }} registros encontrados
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <FilterButton :active-count="filterManager.activeCount" @click="filterManager.isOpen = true" />
                    <Link v-if="can.create" :href="route('users.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest bg-brand text-white shadow-lg shadow-brand/20 hover:scale-[1.02] transition-all">
                        <span class="material-symbols-outlined text-[18px]">person_add</span>
                        Nuevo usuario
                    </Link>
                </div>
            </div>

            <!-- Chips filtros activos -->
            <div v-if="filterManager.activeCount > 0" class="flex flex-wrap items-center gap-2">
                <span
                    class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Filtros
                    activos:</span>
                <span v-if="filterManager.filters.search"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">search</span>
                    "{{ filterManager.filters.search }}"
                    <button @click="filterManager.filters.search = ''"><span
                            class="material-symbols-outlined text-[14px]">close</span></button>
                </span>
                <span v-if="filterManager.filters.is_active !== ''"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">toggle_on</span>
                    {{ filterManager.filters.is_active === '1' ? 'Activos' : 'Inactivos' }}
                    <button @click="filterManager.filters.is_active = ''"><span
                            class="material-symbols-outlined text-[14px]">close</span></button>
                </span>
                <span v-if="filterManager.filters.role !== ''"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">badge</span>
                    {{ ROLE_CONFIG[filterManager.filters.role]?.label ?? filterManager.filters.role }}
                    <button @click="filterManager.filters.role = ''"><span
                            class="material-symbols-outlined text-[14px]">close</span></button>
                </span>
            </div>

            <!-- Patrón único: v-bind="dt.bind" + :columns + @page-change="dt.changePage" -->
            <Datatable v-bind="dt.bind" :columns="columns" @page-change="dt.changePage">

                <template #cell-name="{ row }">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 flex items-center justify-center text-xs font-black shrink-0 border border-slate-200 dark:border-slate-600 uppercase">
                            {{ row.name.charAt(0) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-white truncate">
                                {{ row.name }}
                                <span v-if="row.id === authUser.id"
                                    class="ml-1 text-[9px] font-black text-brand uppercase tracking-tighter">[Tú]</span>
                            </p>
                            <p class="text-[10px] text-slate-500 sm:hidden">{{ row.email }}</p>
                        </div>
                    </div>
                </template>

                <template #cell-company="{ row }">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                        {{ row.company?.name ?? '—' }}
                    </span>
                </template>

                <template #cell-role="{ row }">
                    <span
                        :class="[ROLE_CONFIG[row.role]?.classes, 'px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border border-current/10']">
                        {{ ROLE_CONFIG[row.role]?.label ?? row.role }}
                    </span>
                </template>

                <template #cell-is_active="{ row }">
                    <div class="flex justify-center">
                        <span :class="[
                            row.is_active
                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20'
                                : 'bg-slate-100 text-slate-500 opacity-60',
                            'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border border-current/10 transition-all'
                        ]">
                            <span
                                :class="[row.is_active ? 'bg-emerald-500' : 'bg-slate-400', 'w-1.5 h-1.5 rounded-full']" />
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
            title="Filtrar usuarios" @clear="filterManager.clear()">
            <div class="space-y-6 pt-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Búsqueda
                        rápida</label>
                    <TextInput v-model="filterManager.filters.search" icon="search" placeholder="Nombre o email..." />
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Estado de
                        cuenta</label>
                    <SelectInput v-model="filterManager.filters.is_active" :options="statusOptions" />
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Rol</label>
                    <SelectInput v-model="filterManager.filters.role" :options="roleFilterOptions" />
                </div>
                <div v-if="isSuperAdmin">
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Compañía</label>
                    <SelectInput v-model="filterManager.filters.company_id" :options="companyOptions" />
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
                    ¿Confirmas que deseas desactivar al usuario
                    <span class="font-black text-slate-900 dark:text-white">"{{ deactivateModal.item?.name }}"</span>?
                </p>
                <div
                    class="p-3 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-xl text-[10px] font-black text-red-700 dark:text-red-400 uppercase tracking-widest leading-relaxed">
                    Efecto inmediato: El acceso al sistema será bloqueado hasta su reactivación manual.
                </div>
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
