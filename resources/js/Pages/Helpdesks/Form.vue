<script setup>
// resources/js/Pages/Helpdesks/Form.vue

import { computed, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/shared/components/InputLabel.vue'
import TextInput from '@/shared/components/TextInput.vue'
import SelectInput from '@/shared/components/SelectInput.vue'
import PageHeader from '@/shared/components/PageHeader.vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'
import HelpdeskAgentsSelect from '@/features/Helpdesks/components/HelpdeskAgentsSelect.vue'
import { useAuth } from '@/shared/composables/useAuth'

const props = defineProps({
    helpdesk:  { type: Object, default: null },
    agents:    { type: Array,  default: () => [] },   // agentes de la compañía actual
    allAgents: { type: Array,  default: () => [] },   // todos los agentes (solo super_admin)
    companies: { type: Array,  default: () => [] },   // solo super_admin
    can:       { type: Object, default: () => ({}) },
})

const { isSuperAdmin } = useAuth()
const isEditing = computed(() => !!props.helpdesk)
const pageTitle = computed(() => isEditing.value ? 'Editar Helpdesk' : 'Nuevo Helpdesk')

const form = useForm({
    name:       props.helpdesk?.name       ?? '',
    agent_ids:  props.helpdesk?.agent_ids  ?? [],
    company_id: props.helpdesk?.company_id ?? (props.companies[0]?.id ?? ''),
})

// ── Agentes disponibles según compañía seleccionada ───────────────────────────
// super_admin: filtra allAgents por company_id seleccionado.
// Otros roles:  usa agents directamente (ya filtrado por el controller).
const availableAgents = computed(() => {
    if (isSuperAdmin.value) {
        if (!form.company_id) return []
        return props.allAgents.filter(a => a.company_id == form.company_id)
    }
    return props.agents
})

// Cuando super_admin cambia de compañía, limpiar los agentes seleccionados
// que ya no pertenecen a la nueva compañía.
watch(() => form.company_id, () => {
    if (!isSuperAdmin.value) return
    const validIds = new Set(availableAgents.value.map(a => a.id))
    form.agent_ids = form.agent_ids.filter(id => validIds.has(id))
})

const companyOptions = computed(() =>
    props.companies.map(c => ({ id: c.id, name: c.name }))
)

const submit = () => {
    const method = isEditing.value ? 'put' : 'post'
    const url    = isEditing.value
        ? route('helpdesks.update', props.helpdesk.id)
        : route('helpdesks.store')

    form[method](url, {
        onSuccess: () => router.visit(route('helpdesks.index')),
    })
}
</script>

<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">

            <PageHeader
                :title="pageTitle"
                :description="isEditing
                    ? 'Modifica los datos principales de este helpdesk.'
                    : 'Configura un nuevo departamento de atención.'"
                back-label="Volver a Helpdesks"
                :back-href="route('helpdesks.index')"
            />

            <form @submit.prevent="submit" class="flex flex-col lg:flex-row gap-6 items-start">

                <div class="flex-1 min-w-0 w-full space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                        <div class="px-6 py-8">
                            <div class="flex items-center gap-2 mb-6 text-slate-500">
                                <span class="material-symbols-outlined">support_agent</span>
                                <p class="text-xs font-black uppercase tracking-wider">Información General</p>
                            </div>

                            <div class="max-w-xl space-y-5">

                                <!-- Compañía (solo super_admin) -->
                                <div v-if="isSuperAdmin">
                                    <InputLabel value="Compañía" :required="true" />
                                    <SelectInput
                                        v-model="form.company_id"
                                        :options="companyOptions"
                                        class="mt-1"
                                    />
                                    <p v-if="form.errors.company_id" class="mt-2 text-xs text-danger font-semibold italic">
                                        {{ form.errors.company_id }}
                                    </p>
                                </div>

                                <!-- Nombre -->
                                <div>
                                    <InputLabel value="Nombre del Helpdesk" :required="true" />
                                    <TextInput
                                        v-model="form.name"
                                        icon="label"
                                        placeholder="Ej. Soporte Técnico"
                                        :error="!!form.errors.name"
                                        autofocus
                                    />
                                    <p v-if="form.errors.name" class="mt-2 text-xs text-danger font-semibold italic">
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                            </div>
                        </div>

                        <div class="px-6 py-5 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <SecondaryButton type="button" icon="close" @click="router.visit(route('helpdesks.index'))">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton type="submit" :loading="form.processing" :icon="isEditing ? 'save' : 'add_task'">
                                {{ isEditing ? 'Guardar cambios' : 'Crear Helpdesk' }}
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <!-- Panel lateral: agentes -->
                <aside class="w-full lg:w-96 shrink-0 lg:sticky lg:top-6">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Agentes Asignados</h3>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Selecciona quiénes gestionarán este helpdesk.
                            </p>
                        </div>

                        <!-- Aviso cuando super_admin no ha elegido compañía -->
                        <div v-if="isSuperAdmin && !form.company_id"
                            class="flex items-center gap-2 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 text-xs text-amber-700 dark:text-amber-400">
                            <span class="material-symbols-outlined text-[16px] shrink-0">info</span>
                            Selecciona una compañía para ver sus agentes.
                        </div>

                        <div v-else class="space-y-4">
                            <HelpdeskAgentsSelect v-model="form.agent_ids" :agents="availableAgents" />
                            <p v-if="form.errors.agent_ids" class="mt-2 text-xs text-danger font-semibold">
                                {{ form.errors.agent_ids }}
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2 text-brand">
                                <span class="material-symbols-outlined text-sm">info</span>
                                <p class="text-[10px] font-bold uppercase tracking-tight">
                                    {{ form.agent_ids.length }} agentes seleccionados
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>

            </form>
        </div>
    </AuthenticatedLayout>
</template>
