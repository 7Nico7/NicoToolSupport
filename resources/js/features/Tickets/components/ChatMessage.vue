<script setup>
// resources/js/features/Tickets/ChatMessage.vue
//
// Burbuja de mensaje del chat de ticket.
// — Cliente / usuario: burbuja izquierda, fondo blanco
// — Agente / soporte: burbuja derecha, fondo brand
// — Nota interna: burbuja izquierda, fondo ámbar, badge "Interna"
//
// Muestra imágenes como thumbnails en grid y archivos como chips.

import ImagePreview from '@/shared/components/ImagePreview.vue';
import AttachmentItem from '@/features/Kanban/components/AttachmentItem.vue'
import { computed } from 'vue';
import { useAuth } from '@/shared/composables/useAuth';

const props = defineProps({
    message:   { type: Object,  required: true },
    // ID del usuario logueado — para saber si la burbuja va a la derecha
    authUserId:{ type: Number,  required: true },
    canDelete: { type: Boolean, default: false },
});

const emit = defineEmits(['delete-attachment']);

// La burbuja va a la derecha si el mensaje lo envió el usuario logueado
const isOwn     = computed(() => props.message.user?.id === props.authUserId)
const isInternal= computed(() => props.message.is_internal)

// Separar imágenes de otros archivos
const imageAttachments = computed(() =>
    (props.message.attachments ?? []).filter(a => a.file_category === 'image')
)
const fileAttachments = computed(() =>
    (props.message.attachments ?? []).filter(a => a.file_category !== 'image')
)

const formatDate = (d) => new Date(d).toLocaleString('es-MX', {
    day: '2-digit', month: 'short',
    hour: '2-digit', minute: '2-digit',
})

// Inicial del usuario para el avatar
const initial = computed(() =>
    props.message.user?.name?.charAt(0)?.toUpperCase() ?? '?'
)
</script>

<template>
    <div :class="['flex items-end gap-2.5', isOwn ? 'flex-row-reverse' : 'flex-row']">

        <!-- Avatar -->
        <div :class="[
            'w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0 mb-1',
            isInternal ? 'bg-amber-100 text-amber-700' :
            isOwn      ? 'bg-brand text-white' :
                         'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
        ]">
            {{ initial }}
        </div>

        <!-- Contenido del mensaje -->
        <div :class="['flex flex-col gap-1 max-w-[65%]', isOwn ? 'items-end' : 'items-start']">

            <!-- Meta: nombre + hora + badge interna -->
            <div :class="['flex items-center gap-2', isOwn ? 'flex-row-reverse' : 'flex-row']">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                    {{ message.user?.name ?? 'Desconocido' }}
                </span>
                <span v-if="isInternal"
                    class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-amber-200 dark:bg-amber-800 text-amber-700 dark:text-amber-200"
                >Interna</span>
                <span class="text-[10px] text-slate-400">{{ formatDate(message.created_at) }}</span>
            </div>

            <!-- Burbuja de texto -->
            <div v-if="message.message" :class="[
                'px-4 py-3 text-sm leading-relaxed',
                isInternal
                    ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-900 dark:text-amber-100 rounded-2xl'
                : isOwn
                    ? 'bg-brand text-white rounded-2xl rounded-br-sm shadow-sm shadow-brand/20'
                    : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-2xl rounded-bl-sm shadow-sm',
            ]">
                <p class="whitespace-pre-wrap">{{ message.message }}</p>
            </div>

            <!-- Grid de imágenes adjuntas -->
            <div v-if="imageAttachments.length > 0"
                :class="[
                    'grid gap-1.5',
                    imageAttachments.length === 1 ? 'grid-cols-1' :
                    imageAttachments.length === 2 ? 'grid-cols-2' : 'grid-cols-3'
                ]"
            >
                <ImagePreview
                    v-for="img in imageAttachments"
                    :key="img.id"
                    :src="img.download_url"
                    :filename="img.filename"
                    :alt="img.filename"
                    size="lg"
                    :can-delete="canDelete"
                    @delete="emit('delete-attachment', img.id)"
                />
            </div>

            <!-- Archivos no imagen (chips expandidos) -->
            <div v-if="fileAttachments.length > 0" class="flex flex-col gap-1.5 w-full">
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
    </div>
</template>
