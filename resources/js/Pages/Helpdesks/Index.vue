<script setup>
// resources/js/Pages/Helpdesks/Index.vue

import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ActionMenu from '@/shared/components/ActionMenu.vue'
import SidebarFilter from '@/shared/components/SidebarFilter.vue'
import TextInput   from '@/shared/components/TextInput.vue'
import SelectInput from '@/shared/components/SelectInput.vue'
import Datatable from '@/shared/components/Datatable.vue'
import FilterButton from '@/shared/components/buttons/FilterButton.vue'
import { useFilters } from '@/shared/composables/useFilters'
import { useDatatable } from '@/shared/composables/useDatatable'
import { useAuth } from '@/shared/composables/useAuth'

const props = defineProps({
    helpdesks: { type: Object, required: true },
    filters:   { type: Object, default: () => ({}) },
    companies: { type: Array,  default: () => [] },
    can:       { type: Object, default: () => ({ create: false, edit: false, delete: false }) },
})

const { isSuperAdmin } = useAuth()

const filterManager = useFilters('helpdesks.index', {
    search:     props.filters.search     ?? '',
    company_id: props.filters.company_id ?? '',
})

const dt = useDatatable('helpdesks.index', () => props.helpdesks, filterManager, {
    emptyText: 'No se encontraron helpdesks',
    emptyIcon: 'support_agent',
})

const columns = computed(() => [
    { key: 'name',          label: 'Helpdesk', sortable: true },
    { key: 'agents',        label: 'Agentes',  align: 'left' },
    { key: 'tickets_count', label: 'Tickets',  align: 'center', class: 'w-24' },
])

const companyOptions = computed(() => [
    { id: '', name: 'Todas las compañías' },
    ...props.companies.map(c => ({ id: c.id, name: c.name })),
])

const rowActions = (helpdesk) => {
    const actions = []
    if (props.can.edit) {
        actions.push({
            label: 'Editar', icon: 'edit',
            handler: () => router.visit(route('helpdesks.edit', helpdesk.id)),
        })
    }
    if (props.can.delete) {
        actions.push({ separator: true })
        actions.push({
            label: 'Eliminar', icon: 'delete', variant: 'danger',
            handler: () => {
                if (confirm(`¿Eliminar "${helpdesk.name}"?`)) {
                    router.delete(route('helpdesks.destroy', helpdesk.id), { preserveScroll: true })
                }
            },
        })
    }
    return actions
}
</script>

<template>
    <Head title="Helpdesks" />
    <AuthenticatedLayout>
        <div class="flex flex-col gap-5">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Helpdesks</h1>
                    <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 mt-0.5 uppercase tracking-widest">
                        {{ helpdesks.total }} registros encontrados
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <FilterButton :active-count="filterManager.activeCount" @click="filterManager.isOpen = true" />
                    <Link v-if="can.create" :href="route('helpdesks.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest bg-brand text-white shadow-lg shadow-brand/20 hover:scale-[1.02] transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Nuevo Helpdesk
                    </Link>
                </div>
            </div>

            <div v-if="filterManager.activeCount > 0" class="flex items-center gap-2 flex-wrap">
                <span v-if="filterManager.filters.search"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">search</span>
                    "{{ filterManager.filters.search }}"
                    <button @click="filterManager.filters.search = ''" class="hover:opacity-60 transition-opacity">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </span>
                <span v-if="filterManager.filters.company_id"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand/10 text-brand border border-brand/20">
                    <span class="material-symbols-outlined text-[14px]">business</span>
                    {{ companyOptions.find(c => c.id == filterManager.filters.company_id)?.name ?? filterManager.filters.company_id }}
                    <button @click="filterManager.filters.company_id = ''" class="hover:opacity-60 transition-opacity">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </span>
            </div>

            <Datatable v-bind="dt.bind" :columns="columns" @page-change="dt.changePage">

                <template #cell-name="{ row }">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0 border border-brand/10">
                            <span class="material-symbols-outlined text-[18px]">support_agent</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">{{ row.name }}</p>
                            <p v-if="isSuperAdmin && row.company" class="text-[10px] text-slate-400 uppercase font-black tracking-tighter">
                                {{ row.company.name }}
                            </p>
                        </div>
                    </div>
                </template>

                <template #cell-agents="{ row }">
                    <div v-if="row.users?.length" class="flex items-center -space-x-2">
                        <div v-for="agent in row.users.slice(0, 4)" :key="agent.id"
                            class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 border-2 border-white dark:border-slate-900 flex items-center justify-center text-[9px] font-black text-slate-600 dark:text-slate-300 uppercase shrink-0"
                            :title="agent.name">
                            {{ agent.name.charAt(0) }}
                        </div>
                        <div v-if="row.users.length > 4"
                            class="w-7 h-7 rounded-full bg-brand/10 text-brand border-2 border-white dark:border-slate-900 flex items-center justify-center text-[9px] font-black shrink-0">
                            +{{ row.users.length - 4 }}
                        </div>
                    </div>
                    <span v-else class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Sin agentes</span>
                </template>

                <template #cell-tickets_count="{ row }">
                    <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-50 dark:bg-slate-800/50 text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-700">
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

        <SidebarFilter v-model:open="filterManager.isOpen" :active-count="filterManager.activeCount"
            title="Filtrar Helpdesks" @clear="filterManager.clear()">
            <div class="space-y-6 pt-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Búsqueda</label>
                    <TextInput v-model="filterManager.filters.search" icon="search" placeholder="Nombre del helpdesk o agente..." />
                    <p class="mt-1.5 text-[10px] text-slate-400">Busca por nombre del helpdesk o por nombre del agente asignado.</p>
                </div>
                <div v-if="isSuperAdmin">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Compañía</label>
                    <SelectInput v-model="filterManager.filters.company_id" :options="companyOptions" />
                </div>
            </div>
        </SidebarFilter>
    </AuthenticatedLayout>
</template>
