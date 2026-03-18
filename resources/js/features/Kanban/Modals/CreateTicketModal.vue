<script setup>
// resources/js/features/Kanban/Modals/CreateTicketModal.vue
import { ref, watch, computed } from 'vue';
import Modal from '@/shared/components/Modal.vue';
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue';
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue';
import InputLabel from '@/shared/components/InputLabel.vue';
import TextInput from '@/shared/components/TextInput.vue';
import InputError from '@/shared/components/InputError.vue';
import SelectInput from '@/shared/components/SelectInput.vue';
import AgentSearchInput from '@/features/Kanban/AgentSearchInput.vue';
import { useTicketForm } from '@/features/Kanban/composables/useTicketForm';
import { useKanbanStore } from '@/features/Kanban/stores/kanbanStore';

const props = defineProps({
    show:       { type: Boolean, default: false },
    statuses:   { type: Array,   default: () => [] },
    priorities: { type: Array,   default: () => [] },
    types:      { type: Array,   default: () => [] },
    categories: { type: Array,   default: () => [] },
    projects:   { type: Array,   default: () => [] },
    helpdesks:  { type: Array,   default: () => [] },
});

const emit  = defineEmits(['close', 'created']);
const store = useKanbanStore();
const { form, errors, isValid, reset, handleServerErrors } = useTicketForm();
const saving = ref(false);

watch(() => props.show, (v) => {
    if (v) reset({ status_id: props.statuses[0]?.id ?? null });
});

// ── Opciones para SelectInput ─────────────────────────────────────────────────
// Las opciones con id: null requieren un placeholder en el SelectInput
const statusOptions = computed(() =>
    props.statuses.map(s => ({ id: s.id, name: s.name }))
);
const priorityOptions = computed(() => [
    { id: null, name: 'Sin prioridad' },
    ...props.priorities.map(p => ({ id: p.id, name: p.name })),
]);
const typeOptions = computed(() => [
    { id: null, name: 'Sin tipo' },
    ...props.types.map(t => ({ id: t.id, name: t.name })),
]);
const categoryOptions = computed(() => [
    { id: null, name: 'Sin categoría' },
    ...props.categories.map(c => ({ id: c.id, name: c.name })),
]);
const projectOptions = computed(() => [
    { id: null, name: 'Sin proyecto' },
    ...props.projects.map(p => ({ id: p.id, name: p.name })),
]);
const helpdeskOptions = computed(() => [
    { id: null, name: 'Sin helpdesk' },
    ...props.helpdesks.map(h => ({ id: h.id, name: h.name })),
]);

const submit = async () => {
    if (!isValid.value || saving.value) return;
    saving.value = true;
    try {
        const ticket = await store.createTicket(form.value);
        emit('created', ticket);
        emit('close');
    } catch (err) {
        if (err.response?.status === 422) {
            handleServerErrors(err.response.data.errors ?? {});
        }
    } finally {
        saving.value = false;
    }
};
</script>

<template>
    <Modal :show="show" title="Nuevo Ticket" size="lg" @close="$emit('close')">

        <template #icon>
            <span class="material-symbols-outlined text-brand text-xl">confirmation_number</span>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

            <!-- Título -->
            <div class="md:col-span-2">
                <InputLabel value="Título" required />
                <TextInput
                    v-model="form.title"
                    placeholder="Describe el problema..."
                    icon="title"
                    :error="!!errors.title"
                    class="mt-1"
                />
                <InputError :message="errors.title?.[0]" />
            </div>

            <!-- Descripción -->
            <!-- <textarea> nativo se mantiene — no existe TextareaInput como componente compartido -->
            <div class="md:col-span-2">
                <InputLabel value="Descripción" />
                <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Detalla el problema o solicitud..."
                    class="mt-1 w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none transition-all"
                />
            </div>

            <!-- Estado -->
            <!-- <select> → SelectInput -->
            <div>
                <InputLabel value="Estado" required />
                <SelectInput
                    v-model="form.status_id"
                    :options="statusOptions"
                    class="mt-1"
                />
            </div>

            <!-- Prioridad -->
            <div>
                <InputLabel value="Prioridad" />
                <SelectInput
                    v-model="form.priority_id"
                    :options="priorityOptions"
                    class="mt-1"
                />
            </div>

            <!-- Tipo -->
            <div>
                <InputLabel value="Tipo" />
                <SelectInput
                    v-model="form.type_id"
                    :options="typeOptions"
                    class="mt-1"
                />
            </div>

            <!-- Categoría -->
            <div>
                <InputLabel value="Categoría" />
                <SelectInput
                    v-model="form.category_id"
                    :options="categoryOptions"
                    class="mt-1"
                />
            </div>

            <!-- Proyecto -->
            <div>
                <InputLabel value="Proyecto" />
                <SelectInput
                    v-model="form.project_id"
                    :options="projectOptions"
                    class="mt-1"
                />
            </div>

            <!-- Helpdesk -->
            <div>
                <InputLabel value="Helpdesk" />
                <SelectInput
                    v-model="form.helpdesk_id"
                    :options="helpdeskOptions"
                    class="mt-1"
                />
            </div>

            <!-- Asignado a -->
            <div>
                <InputLabel value="Asignar a" />
                <div class="mt-1">
                    <AgentSearchInput v-model="form.assigned_to" />
                </div>
            </div>

            <!-- Fecha de vencimiento -->
            <div>
                <InputLabel value="Fecha de vencimiento" />
                <TextInput
                    v-model="form.due_date"
                    type="date"
                    icon="calendar_month"
                    class="mt-1"
                />
            </div>
        </div>

        <template #footer>
            <SecondaryButton @click="$emit('close')">Cancelar</SecondaryButton>
            <PrimaryButton
                icon="add"
                :loading="saving"
                :disabled="!isValid"
                @click="submit"
            >
                Crear Ticket
            </PrimaryButton>
        </template>
    </Modal>
</template>
