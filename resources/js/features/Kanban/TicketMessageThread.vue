<script setup>
// resources/js/features/Kanban/TicketMessageThread.vue
import { ref, computed, watch, nextTick } from 'vue';
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue';
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue';
import Checkbox from '@/shared/components/Checkbox.vue';
import AttachmentItem from '@/shared/components/AttachmentItem.vue';

const props = defineProps({
    messages:    { type: Array,   default: () => [] },
    activities:  { type: Array,   default: () => [] },
    // Evidencias directas del ticket (tab Archivos)
    attachments: { type: Array,   default: () => [] },
    ticketId:    { type: Number,  required: true },
    canInternal: { type: Boolean, default: true },
    // true = mostrar indicador "En vivo" (canal Echo activo)
    isLive:      { type: Boolean, default: false },
    // true = el usuario puede eliminar adjuntos
    canDelete:   { type: Boolean, default: false },
});

const emit = defineEmits([
    'send',
    // Evidencias del ticket
    'upload-ticket-attachment',
    'delete-ticket-attachment',
    // Evidencias de mensaje (eliminar)
    'delete-message-attachment',
]);

// ── Estado del compose ────────────────────────────────────────────────────────
const newMessage   = ref('');
const isInternal   = ref(false);
const sending      = ref(false);
const activeTab    = ref('messages');

// Archivos pendientes en el compose (antes de enviar con el mensaje)
const pendingFiles = ref([]);
const fileInputRef = ref(null);

// ── Estado de la tab Archivos ─────────────────────────────────────────────────
const uploadingFile   = ref(false);
const newDescription  = ref('');
const fileToUpload    = ref(null);
const ticketFileInput = ref(null);

// ── Ref al hilo de mensajes para auto-scroll ──────────────────────────────────
const threadEl = ref(null);

// ── Computed ──────────────────────────────────────────────────────────────────
const sortedMessages = computed(() =>
    [...props.messages].sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
);

const sortedActivity = computed(() =>
    [...props.activities].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
);

const sortedAttachments = computed(() =>
    [...props.attachments].sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
);

// ── Auto-scroll ───────────────────────────────────────────────────────────────
const isNearBottom = () => {
    if (!threadEl.value) return true;
    const { scrollTop, scrollHeight, clientHeight } = threadEl.value;
    return scrollHeight - scrollTop - clientHeight <= 80;
};

const scrollToBottom = () => {
    nextTick(() => {
        if (threadEl.value) threadEl.value.scrollTop = threadEl.value.scrollHeight;
    });
};

watch(
    sortedMessages,
    (newVal, oldVal) => {
        if (oldVal && newVal.length > oldVal.length && isNearBottom()) scrollToBottom();
        if (!oldVal || oldVal.length === 0) scrollToBottom();
    },
    { deep: false }
);

// ── Enviar mensaje (+ archivos pendientes) ────────────────────────────────────
const send = () => {
    if (!newMessage.value.trim() && pendingFiles.value.length === 0) return;
    if (sending.value) return;

    sending.value = true;
    emit('send', {
        message:     newMessage.value.trim(),
        is_internal: isInternal.value,
        files:       [...pendingFiles.value],
        onDone: () => {
            newMessage.value  = '';
            pendingFiles.value = [];
            sending.value     = false;
            if (fileInputRef.value) fileInputRef.value.value = '';
            scrollToBottom();
        },
        onError: () => { sending.value = false; },
    });
};

// ── Selección de archivos en el compose ───────────────────────────────────────
const onComposeFilesSelected = (e) => {
    const files = Array.from(e.target.files ?? []);
    pendingFiles.value = [...pendingFiles.value, ...files];
    // Resetear input para permitir seleccionar el mismo archivo de nuevo
    e.target.value = '';
};

const removePendingFile = (index) => {
    pendingFiles.value = pendingFiles.value.filter((_, i) => i !== index);
};

// ── Subir evidencia directa al ticket ─────────────────────────────────────────
const onTicketFileSelected = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    fileToUpload.value = file;
    e.target.value = '';
};

const submitTicketAttachment = () => {
    if (!fileToUpload.value || uploadingFile.value) return;
    uploadingFile.value = true;
    emit('upload-ticket-attachment', {
        file:        fileToUpload.value,
        description: newDescription.value.trim(),
        onDone: () => {
            fileToUpload.value  = null;
            newDescription.value = '';
            uploadingFile.value  = false;
        },
        onError: () => { uploadingFile.value = false; },
    });
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatDate = (d) => new Date(d).toLocaleString('es-MX', {
    day: '2-digit', month: 'short',
    hour: '2-digit', minute: '2-digit',
});

const formatFileSize = (bytes) => {
    if (bytes >= 1_048_576) return `${(bytes / 1_048_576).toFixed(1)} MB`;
    if (bytes >= 1_024)     return `${(bytes / 1_024).toFixed(0)} KB`;
    return `${bytes} B`;
};

const actionIcon = {
    created:       'add_circle',
    updated:       'edit',
    moved:         'swap_horiz',
    assigned:      'person_add',
    message_added: 'chat',
};
</script>

<template>
    <div class="flex flex-col h-full">

        <!-- ── Tabs ───────────────────────────────────────────────────────── -->
        <div class="flex border-b border-slate-200 dark:border-slate-700 mb-4 shrink-0">
            <button
                v-for="tab in [
                    { id: 'messages',    label: 'Mensajes',  icon: 'chat_bubble' },
                    { id: 'activity',    label: 'Actividad', icon: 'history'     },
                    { id: 'attachments', label: 'Archivos',  icon: 'folder_open' },
                ]"
                :key="tab.id"
                type="button"
                @click="activeTab = tab.id"
                :class="[
                    'flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-colors',
                    activeTab === tab.id
                        ? 'border-brand text-brand'
                        : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300',
                ]"
            >
                <span class="material-symbols-outlined text-base">{{ tab.icon }}</span>
                {{ tab.label }}

                <!-- Badge contador -->
                <span
                    v-if="tab.id === 'messages' && messages.length"
                    class="text-[10px] font-black bg-brand/10 text-brand rounded-full px-1.5 py-0.5"
                >{{ messages.length }}</span>

                <span
                    v-if="tab.id === 'attachments' && attachments.length"
                    class="text-[10px] font-black bg-brand/10 text-brand rounded-full px-1.5 py-0.5"
                >{{ attachments.length }}</span>

                <!-- Indicador En vivo -->
                <span
                    v-if="tab.id === 'messages' && isLive"
                    class="flex items-center gap-1 ml-1 text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider"
                    title="Mensajes en tiempo real activos"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                    En vivo
                </span>
            </button>
        </div>

        <!-- ── Tab: Mensajes ──────────────────────────────────────────────── -->
        <template v-if="activeTab === 'messages'">
            <div ref="threadEl" class="flex-1 overflow-y-auto space-y-3 pr-1 mb-4 custom-scrollbar">

                <div
                    v-for="msg in sortedMessages"
                    :key="msg.id"
                    :class="[
                        'rounded-xl px-4 py-3 max-w-[90%]',
                        msg.is_internal
                            ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800'
                            : 'bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700',
                    ]"
                >
                    <!-- Header del mensaje -->
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <div class="h-5 w-5 rounded-md bg-brand/10 text-brand flex items-center justify-center text-[10px] font-bold">
                                {{ msg.user?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                            </div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ msg.user?.name ?? 'Desconocido' }}
                            </span>
                            <span
                                v-if="msg.is_internal"
                                class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-amber-200 dark:bg-amber-800 text-amber-700 dark:text-amber-200"
                            >Interna</span>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ formatDate(msg.created_at) }}</span>
                    </div>

                    <!-- Texto del mensaje -->
                    <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">
                        {{ msg.message }}
                    </p>

                    <!-- Adjuntos del mensaje (chips) -->
                    <div v-if="msg.attachments?.length" class="flex flex-wrap gap-1.5 mt-2 pt-2 border-t border-slate-200/50 dark:border-slate-700/50">
                        <AttachmentItem
                            v-for="att in msg.attachments"
                            :key="att.id"
                            :attachment="att"
                            :can-delete="canDelete"
                            variant="chip"
                            @delete="emit('delete-message-attachment', { messageId: msg.id, attachmentId: $event })"
                        />
                    </div>
                </div>

                <div v-if="sortedMessages.length === 0" class="flex flex-col items-center py-8 text-slate-400">
                    <span class="material-symbols-outlined text-3xl mb-2">chat_bubble_outline</span>
                    <p class="text-sm">Sin mensajes aún</p>
                </div>
            </div>

            <!-- Compose -->
            <div class="shrink-0 border-t border-slate-200 dark:border-slate-700 pt-3">

                <!-- Archivos pendientes -->
                <div v-if="pendingFiles.length > 0" class="flex flex-wrap gap-1.5 mb-2">
                    <span
                        v-for="(file, i) in pendingFiles"
                        :key="i"
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-semibold bg-brand/10 text-brand border border-brand/20"
                    >
                        <span class="material-symbols-outlined text-[13px]">attach_file</span>
                        <span class="truncate max-w-[120px]">{{ file.name }}</span>
                        <span class="text-brand/60">{{ formatFileSize(file.size) }}</span>
                        <button type="button" @click="removePendingFile(i)" class="hover:opacity-60">
                            <span class="material-symbols-outlined text-[12px]">close</span>
                        </button>
                    </span>
                </div>

                <textarea
                    v-model="newMessage"
                    rows="3"
                    placeholder="Escribe un mensaje..."
                    @keydown.ctrl.enter="send"
                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white resize-none focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition-all mb-2"
                />

                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <Checkbox
                            v-if="canInternal"
                            v-model="isInternal"
                            label="Nota interna"
                        />
                        <div v-else />
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Botón adjuntar archivos al mensaje -->
                        <input
                            ref="fileInputRef"
                            type="file"
                            multiple
                            accept="image/*,.pdf,.xlsx,.xls,.docx,.doc,.csv,.zip"
                            class="hidden"
                            @change="onComposeFilesSelected"
                        />
                        <button
                            type="button"
                            class="flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-brand hover:bg-brand/10 transition-colors"
                            title="Adjuntar archivos"
                            @click="fileInputRef?.click()"
                        >
                            <span class="material-symbols-outlined text-[18px]">attach_file</span>
                        </button>

                        <PrimaryButton
                            size="sm"
                            icon="send"
                            :loading="sending"
                            :disabled="!newMessage.trim() && pendingFiles.length === 0"
                            @click="send"
                        >
                            Enviar
                        </PrimaryButton>
                    </div>
                </div>

                <p class="text-[10px] text-slate-400 mt-1">Ctrl+Enter para enviar</p>
            </div>
        </template>

        <!-- ── Tab: Actividad ─────────────────────────────────────────────── -->
        <div v-else-if="activeTab === 'activity'" class="flex-1 overflow-y-auto space-y-2 custom-scrollbar">
            <div
                v-for="act in sortedActivity"
                :key="act.id"
                class="flex items-start gap-3 py-2 border-b border-slate-100 dark:border-slate-800 last:border-0"
            >
                <div class="h-7 w-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm text-slate-500">
                        {{ actionIcon[act.action] ?? 'info' }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-700 dark:text-slate-300">{{ act.description }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">
                        {{ act.user?.name ?? 'Sistema' }} · {{ formatDate(act.created_at) }}
                    </p>
                </div>
            </div>

            <div v-if="sortedActivity.length === 0" class="flex flex-col items-center py-8 text-slate-400">
                <span class="material-symbols-outlined text-3xl mb-2">history</span>
                <p class="text-sm">Sin actividad registrada</p>
            </div>
        </div>

        <!-- ── Tab: Archivos ──────────────────────────────────────────────── -->
        <div v-else-if="activeTab === 'attachments'" class="flex flex-col flex-1 min-h-0">

            <!-- Lista de evidencias del ticket -->
            <div class="flex-1 overflow-y-auto space-y-2 pr-1 mb-4 custom-scrollbar">
                <AttachmentItem
                    v-for="att in sortedAttachments"
                    :key="att.id"
                    :attachment="att"
                    :can-delete="canDelete"
                    variant="row"
                    @delete="emit('delete-ticket-attachment', $event)"
                />

                <div v-if="sortedAttachments.length === 0" class="flex flex-col items-center py-8 text-slate-400">
                    <span class="material-symbols-outlined text-3xl mb-2">folder_open</span>
                    <p class="text-sm">Sin evidencias adjuntas</p>
                    <p class="text-xs mt-1">Sube capturas de pantalla, PDFs o documentos para resolver el ticket más rápido.</p>
                </div>
            </div>

            <!-- Uploader de evidencia del ticket -->
            <div class="shrink-0 border-t border-slate-200 dark:border-slate-700 pt-3 space-y-2">

                <!-- Archivo seleccionado -->
                <div v-if="fileToUpload" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-brand/5 border border-brand/20">
                    <span class="material-symbols-outlined text-brand text-[18px]">attach_file</span>
                    <span class="text-xs font-semibold text-brand truncate flex-1">{{ fileToUpload.name }}</span>
                    <span class="text-[10px] text-brand/60 shrink-0">{{ formatFileSize(fileToUpload.size) }}</span>
                    <button type="button" @click="fileToUpload = null" class="text-brand/60 hover:text-brand">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </div>

                <!-- Descripción (opcional) -->
                <input
                    v-if="fileToUpload"
                    v-model="newDescription"
                    type="text"
                    placeholder="Descripción (opcional) — ¿qué muestra este archivo?"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition-all"
                />

                <!-- Acciones -->
                <div class="flex items-center gap-2">
                    <input
                        ref="ticketFileInput"
                        type="file"
                        accept="image/*,.pdf,.xlsx,.xls,.docx,.doc,.csv,.zip"
                        class="hidden"
                        @change="onTicketFileSelected"
                    />
                    <button
                        type="button"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-brand hover:text-brand bg-white dark:bg-slate-800 transition-all"
                        @click="ticketFileInput?.click()"
                    >
                        <span class="material-symbols-outlined text-[16px]">upload</span>
                        {{ fileToUpload ? 'Cambiar archivo' : 'Seleccionar archivo' }}
                    </button>

                    <PrimaryButton
                        v-if="fileToUpload"
                        size="sm"
                        icon="cloud_upload"
                        :loading="uploadingFile"
                        @click="submitTicketAttachment"
                    >
                        Subir evidencia
                    </PrimaryButton>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-200 dark:bg-slate-700 rounded-full;
}
</style>
