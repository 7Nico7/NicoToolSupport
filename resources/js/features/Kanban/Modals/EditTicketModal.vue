<script setup>
// resources/js/features/Kanban/Modals/EditTicketModal.vue
import { ref, watch, computed } from 'vue';
import Modal from '@/shared/components/Modal.vue';
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue';
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue';
import DangerButton from '@/shared/components/buttons/DangerButton.vue';
import InputLabel from '@/shared/components/InputLabel.vue';
import TextInput from '@/shared/components/TextInput.vue';
import SelectInput from '@/shared/components/SelectInput.vue';
import AgentSearchInput from '@/features/Kanban/AgentSearchInput.vue';
import TicketMessageThread from '@/features/Kanban/TicketMessageThread.vue';
import { useTicketForm } from '@/features/Kanban/composables/useTicketForm';
import { useKanbanStore } from '@/features/Kanban/stores/kanbanStore';
import { useTicketChannel } from '@/features/Kanban/composables/useTicketChannel';
import { useAuth } from '@/shared/composables/useAuth';

const props = defineProps({
    show:          { type: Boolean,        default: false },
    ticketId:      { type: [Number, null], default: null },
    statuses:      { type: Array,          default: () => [] },
    priorities:    { type: Array,          default: () => [] },
    types:         { type: Array,          default: () => [] },
    categories:    { type: Array,          default: () => [] },
    projects:      { type: Array,          default: () => [] },
    helpdesks:     { type: Array,          default: () => [] },
    // super_admin
    companies:     { type: Array,          default: () => [] },
    isSuperAdmin:  { type: Boolean,        default: false },
    allProjects:   { type: Array,          default: () => [] },
    allHelpdesks:  { type: Array,          default: () => [] },
    allCategories: { type: Array,          default: () => [] },
    canInternal:   { type: Boolean,        default: true },
    canDelete:     { type: Boolean,        default: false },
});

const emit  = defineEmits(['close', 'updated', 'deleted']);
const store = useKanbanStore();
const { authUser } = useAuth();
const { form, errors, populate, handleServerErrors } = useTicketForm();

const ticket    = ref(null);
const loading   = ref(false);
const saving    = ref(false);
const loadError = ref(null);

// Compañía activa del ticket — solo relevante para super_admin
const ticketCompanyId = ref(null);

// ── Canal en tiempo real ──────────────────────────────────────────────────────
useTicketChannel(
    () => props.show ? props.ticketId : null,
    (msg) => {
        if (!ticket.value) return;
        const alreadyExists = ticket.value.messages?.some(m => m.id === msg.id);
        if (!alreadyExists) {
            ticket.value = {
                ...ticket.value,
                messages:       [...(ticket.value.messages ?? []), msg],
                messages_count: (ticket.value.messages_count ?? 0) + 1,
            };
        }
    }
);

// ── Cargar ticket ─────────────────────────────────────────────────────────────
watch(() => props.show, async (v) => {
    if (v && props.ticketId) {
        loading.value   = true;
        loadError.value = null;
        try {
            ticket.value      = await store.refreshTicket(props.ticketId);
            ticketCompanyId.value = ticket.value?.company_id ?? null;
            populate(ticket.value);
        } catch (err) {
            loadError.value = err.response?.data?.message
                ?? `Error ${err.response?.status ?? ''}: no se pudo cargar el ticket.`;
        } finally {
            loading.value = false;
        }
    } else if (!v) {
        ticket.value        = null;
        loadError.value     = null;
        ticketCompanyId.value = null;
    }
});

// ── Catálogos filtrados por compañía del ticket ───────────────────────────────
const activeProjects = computed(() => {
    if (!props.isSuperAdmin) return props.projects;
    if (!ticketCompanyId.value) return props.projects;
    return props.allProjects.filter(p => p.company_id == ticketCompanyId.value);
});

const activeHelpdesks = computed(() => {
    if (!props.isSuperAdmin) return props.helpdesks;
    if (!ticketCompanyId.value) return props.helpdesks;
    return props.allHelpdesks.filter(h => h.company_id == ticketCompanyId.value);
});

const activeCategories = computed(() => {
    if (!props.isSuperAdmin) return props.categories;
    if (!ticketCompanyId.value) return props.categories;
    return props.allCategories.filter(c => c.company_id == ticketCompanyId.value);
});

// ── Computed ──────────────────────────────────────────────────────────────────
const assigneeName = computed(() => ticket.value?.assigned_user?.name ?? '');

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
    ...activeCategories.value.map(c => ({ id: c.id, name: c.name })),
]);
const projectOptions = computed(() => [
    { id: null, name: 'Sin proyecto' },
    ...activeProjects.value.map(p => ({ id: p.id, name: p.name })),
]);

const canDeleteAttachments = computed(() =>
    props.canDelete || ['admin', 'super_admin'].includes(authUser.value?.role)
);

// Nombre de compañía del ticket (solo para super_admin, informativo)
const ticketCompanyName = computed(() => {
    if (!props.isSuperAdmin || !ticketCompanyId.value) return null;
    return props.companies.find(c => c.id == ticketCompanyId.value)?.name ?? null;
});

// ── Acciones ──────────────────────────────────────────────────────────────────
const submit = async () => {
    if (saving.value) return;
    saving.value = true;
    try {
        const updated = await store.updateTicket(props.ticketId, form.value);
        ticket.value  = updated;
        emit('updated', updated);
        emit('close');
    } catch (err) {
        if (err.response?.status === 422) {
            handleServerErrors(err.response.data.errors ?? {});
        }
    } finally {
        saving.value = false;
    }
};

const confirmDelete = async () => {
    if (!confirm('¿Eliminar este ticket permanentemente?')) return;
    await store.deleteTicket(props.ticketId);
    emit('deleted', props.ticketId);
    emit('close');
};

const sendMessage = async ({ message, is_internal, files = [], onDone, onError }) => {
    try {
        const newMsg = await store.addMessage(props.ticketId, message, is_internal, files);
        if (ticket.value && newMsg) {
            const alreadyExists = ticket.value.messages?.some(m => m.id === newMsg.id);
            if (!alreadyExists) {
                ticket.value = {
                    ...ticket.value,
                    messages:       [...(ticket.value.messages ?? []), newMsg],
                    messages_count: (ticket.value.messages_count ?? 0) + 1,
                };
            }
        }
        onDone();
    } catch { onError(); }
};

const handleDeleteMessageAttachment = async ({ messageId, attachmentId }) => {
    await store.deleteMessageAttachment(messageId, attachmentId);
    if (!ticket.value?.messages) return;
    const msg = ticket.value.messages.find(m => m.id === messageId);
    if (msg?.attachments) {
        msg.attachments = msg.attachments.filter(a => a.id !== attachmentId);
    }
};

const handleUploadTicketAttachment = async ({ file, description, onDone, onError }) => {
    try {
        const attachment = await store.uploadTicketAttachment(props.ticketId, file, description);
        if (ticket.value) {
            ticket.value = { ...ticket.value, attachments: [...(ticket.value.attachments ?? []), attachment] };
        }
        onDone();
    } catch { onError(); }
};

const handleDeleteTicketAttachment = async (attachmentId) => {
    await store.deleteTicketAttachment(props.ticketId, attachmentId);
    if (ticket.value?.attachments) {
        ticket.value = { ...ticket.value, attachments: ticket.value.attachments.filter(a => a.id !== attachmentId) };
    }
};
</script>

<template>
    <Modal :show="show" size="2xl" @close="$emit('close')">

        <template #header>
            <div class="flex items-center gap-3 min-w-0">
                <span class="shrink-0 text-xs font-black text-brand bg-brand/10 px-2.5 py-1 rounded-lg tracking-widest">
                    {{ ticket?.ticket_number ?? '···' }}
                </span>
                <h3 class="text-base font-bold text-slate-900 dark:text-white truncate">
                    {{ ticket?.title ?? 'Cargando...' }}
                </h3>
                <!-- Chip compañía del ticket (solo super_admin, informativo) -->
                <span v-if="ticketCompanyName"
                    class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-lg
                           text-[10px] font-black bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400
                           border border-blue-100 dark:border-blue-800">
                    <span class="material-symbols-outlined text-[12px]">business</span>
                    {{ ticketCompanyName }}
                </span>
            </div>
        </template>

        <div v-if="loading" class="flex items-center justify-center py-20 text-slate-400">
            <span class="material-symbols-outlined text-4xl animate-spin">progress_activity</span>
        </div>

        <div v-else-if="loadError" class="flex flex-col items-center justify-center py-16 gap-4 text-center">
            <span class="material-symbols-outlined text-5xl text-danger">error_outline</span>
            <div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">No se pudo cargar el ticket</p>
                <p class="text-xs text-slate-500 mt-1">{{ loadError }}</p>
            </div>
            <SecondaryButton size="sm" icon="close" @click="$emit('close')">Cerrar</SecondaryButton>
        </div>

        <div v-else class="flex flex-col lg:flex-row gap-6 h-[580px] overflow-hidden">

            <!-- Panel izquierdo: detalles -->
            <div class="lg:w-80 shrink-0 space-y-4">
                <p class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                    Detalles del ticket
                </p>

                <div>
                    <InputLabel value="Estado" />
                    <SelectInput v-model="form.status_id" :options="statusOptions" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Prioridad" />
                    <SelectInput v-model="form.priority_id" :options="priorityOptions" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Tipo" />
                    <SelectInput v-model="form.type_id" :options="typeOptions" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Categoría" />
                    <SelectInput v-model="form.category_id" :options="categoryOptions" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Asignado a" />
                    <div class="mt-1">
                        <AgentSearchInput
                            v-model="form.assigned_to"
                            :initial-name="assigneeName"
                            :company-id="isSuperAdmin ? ticketCompanyId : null"
                        />
                    </div>
                </div>
                <div>
                    <InputLabel value="Proyecto" />
                    <SelectInput v-model="form.project_id" :options="projectOptions" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Vencimiento" />
                    <TextInput v-model="form.due_date" type="date" icon="calendar_month" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Descripción" />
                    <textarea v-model="form.description" rows="4"
                        class="mt-1 w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none transition-all" />
                </div>
            </div>

            <!-- Panel derecho: mensajes + archivos -->
            <div class="flex-1 min-h-0 flex flex-col overflow-hidden">
                <TicketMessageThread
                    v-if="ticket"
                    :ticket-id="ticketId"
                    :messages="ticket.messages ?? []"
                    :activities="ticket.activities ?? []"
                    :attachments="ticket.attachments ?? []"
                    :can-internal="canInternal"
                    :can-delete="canDeleteAttachments"
                    :is-live="true"
                    class="flex-1"
                    @send="sendMessage"
                    @upload-ticket-attachment="handleUploadTicketAttachment"
                    @delete-ticket-attachment="handleDeleteTicketAttachment"
                    @delete-message-attachment="handleDeleteMessageAttachment"
                />
            </div>
        </div>

        <template #footer>
            <DangerButton v-if="canDelete" variant="ghost" icon="delete" class="mr-auto" @click="confirmDelete">
                Eliminar
            </DangerButton>
            <SecondaryButton @click="$emit('close')">Cancelar</SecondaryButton>
            <PrimaryButton icon="save" :loading="saving" @click="submit">Guardar</PrimaryButton>
        </template>
    </Modal>
</template>
