<script setup>
// resources/js/features/tickets/components/TicketInfoPanel.vue
import { ref, computed } from 'vue'
import AttachmentItem from '@/features/Kanban/components/AttachmentItem.vue'
import ImagePreview from '@/shared/components/ImagePreview.vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'

const props = defineProps({
    ticket:    { type: Object,  required: true },
    canDelete: { type: Boolean, default: false },
    uploading: { type: Boolean, default: false },
})

const emit = defineEmits(['upload-attachment', 'delete-attachment'])

const fileToUpload    = ref(null)
const description     = ref('')
const ticketFileInput = ref(null)
const activeSection   = ref('details')

const onFileSelected = (e) => {
    fileToUpload.value = e.target.files?.[0] ?? null
    e.target.value = ''
}

const submitUpload = () => {
    if (!fileToUpload.value || props.uploading) return
    emit('upload-attachment', {
        file:        fileToUpload.value,
        description: description.value.trim(),
        onDone: () => { fileToUpload.value = null; description.value = '' },
        onError: () => {},
    })
}

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
    : '—'

const imageAttachments = computed(() =>
    (props.ticket.attachments ?? []).filter(a => a.file_category === 'image')
)
const fileAttachments = computed(() =>
    (props.ticket.attachments ?? []).filter(a => a.file_category !== 'image')
)
</script>

<template>
    <aside class="w-72 shrink-0 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-700 flex flex-col overflow-hidden">

        <!-- Tabs -->
        <div class="flex border-b border-slate-200 dark:border-slate-700 shrink-0">
            <button
                v-for="tab in [{ id: 'details', label: 'Detalles', icon: 'info' }, { id: 'evidence', label: 'Evidencias', icon: 'folder_open' }]"
                :key="tab.id"
                type="button"
                :class="[
                    'flex-1 flex items-center justify-center gap-1.5 py-3 text-xs font-bold transition-colors border-b-2 -mb-px',
                    activeSection === tab.id ? 'border-brand text-brand' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300',
                ]"
                @click="activeSection = tab.id"
            >
                <span class="material-symbols-outlined text-[15px]">{{ tab.icon }}</span>
                {{ tab.label }}
                <span
                    v-if="tab.id === 'evidence' && ticket.attachments?.length"
                    class="text-[9px] font-black bg-brand/10 text-brand rounded-full px-1.5 py-0.5"
                >{{ ticket.attachments.length }}</span>
            </button>
        </div>

        <!-- ── Tab: Detalles ──────────────────────────────────────────────── -->
        <div v-if="activeSection === 'details'" class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">

            <!-- Perfil del cliente -->
            <section class="p-5 border-b border-slate-100 dark:border-slate-800">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Cliente</p>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-brand/10 text-brand flex items-center justify-center text-sm font-black shrink-0">
                        {{ ticket.created_user?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ ticket.created_user?.name ?? '—' }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ ticket.created_user?.email ?? '' }}</p>
                    </div>
                </div>
            </section>

            <!-- Detalles -->
            <section class="p-5 border-b border-slate-100 dark:border-slate-800 space-y-3">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Detalles</p>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Estado</span>
                    <span v-if="ticket.status" class="text-[10px] font-black px-2 py-0.5 rounded-full"
                        :style="{ backgroundColor: ticket.status.color + '20', color: ticket.status.color }">
                        {{ ticket.status.name }}
                    </span>
                    <span v-else class="text-xs text-slate-400">—</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Prioridad</span>
                    <div v-if="ticket.priority" class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: ticket.priority.color }" />
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ ticket.priority.name }}</span>
                    </div>
                    <span v-else class="text-xs text-slate-400">—</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Tipo</span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ ticket.type?.name ?? '—' }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Categoría</span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ ticket.category?.name ?? '—' }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Asignado a</span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[120px]">
                        {{ ticket.assigned_user?.name ?? 'Sin asignar' }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Vencimiento</span>
                    <span :class="['text-xs font-bold', ticket.due_date && new Date(ticket.due_date) < new Date() ? 'text-danger' : 'text-slate-700 dark:text-slate-300']">
                        {{ formatDate(ticket.due_date) }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Creado</span>
                    <span class="text-xs text-slate-500">{{ formatDate(ticket.created_at) }}</span>
                </div>
            </section>

            <!-- Descripción -->
            <section v-if="ticket.description" class="p-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Descripción</p>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-wrap">{{ ticket.description }}</p>
            </section>
        </div>

        <!-- ── Tab: Evidencias ────────────────────────────────────────────── -->
        <div v-else-if="activeSection === 'evidence'" class="flex-1 min-h-0 flex flex-col overflow-hidden">

            <!-- Lista scrollable -->
            <div class="flex-1 min-h-0 overflow-y-auto p-4 space-y-3 custom-scrollbar">

                <!-- Grid imágenes -->
                <div v-if="imageAttachments.length > 0">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Imágenes</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div v-for="img in imageAttachments" :key="img.id" class="flex flex-col gap-1">
                            <ImagePreview
                                :src="img.download_url"
                                :filename="img.filename"
                                :alt="img.filename"
                                size="md"
                                :can-delete="canDelete"
                                @delete="emit('delete-attachment', img.id)"
                            />
                            <p v-if="img.description" class="text-[10px] text-slate-500 leading-snug px-0.5">{{ img.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Archivos -->
                <div v-if="fileAttachments.length > 0">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Archivos</p>
                    <div class="space-y-2">
                        <AttachmentItem
                            v-for="file in fileAttachments"
                            :key="file.id"
                            :attachment="file"
                            :can-delete="canDelete"
                            variant="row"
                            @delete="emit('delete-attachment', file.id)"
                        />
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="!ticket.attachments?.length" class="flex flex-col items-center py-10 text-slate-400">
                    <span class="material-symbols-outlined text-3xl mb-2">folder_open</span>
                    <p class="text-xs text-center">Sin evidencias adjuntas.</p>
                </div>
            </div>

            <!-- Uploader (siempre visible, no scrollea) -->
            <div class="shrink-0 border-t border-slate-200 dark:border-slate-700 p-4 space-y-2">
                <div v-if="fileToUpload"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl bg-brand/5 border border-brand/20"
                >
                    <span class="material-symbols-outlined text-brand text-[16px]">attach_file</span>
                    <span class="text-xs font-semibold text-brand truncate flex-1">{{ fileToUpload.name }}</span>
                    <button type="button" @click="fileToUpload = null" class="text-brand/60 hover:text-brand">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </div>

                <input v-if="fileToUpload" v-model="description" type="text"
                    placeholder="Descripción (opcional)"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand"
                />

                <div class="flex gap-2">
                    <input ref="ticketFileInput" type="file"
                        accept="image/*,.pdf,.xlsx,.xls,.docx,.doc,.csv,.zip"
                        class="hidden" @change="onFileSelected"
                    />
                    <button type="button"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-brand hover:text-brand transition-all"
                        @click="ticketFileInput?.click()"
                    >
                        <span class="material-symbols-outlined text-[15px]">upload</span>
                        {{ fileToUpload ? 'Cambiar' : 'Subir evidencia' }}
                    </button>

                    <PrimaryButton v-if="fileToUpload" size="sm" icon="cloud_upload" :loading="uploading" @click="submitUpload">
                        Subir
                    </PrimaryButton>
                </div>
            </div>
        </div>

    </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-slate-200 dark:bg-slate-700 rounded-full; }
</style>
