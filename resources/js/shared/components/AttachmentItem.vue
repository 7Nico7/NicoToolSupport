<script setup>
// resources/js/shared/components/AttachmentItem.vue
//
// Renderiza un archivo adjunto individual.
// Usado tanto en la tab "Archivos" (ticket-level) como inline en mensajes.
// El download_url ya viene del backend como URL firmada con expiración.

const props = defineProps({
    attachment:  { type: Object,  required: true },
    // false = solo el usuario dueño o admin puede ver el botón eliminar
    canDelete:   { type: Boolean, default: false },
    // 'row' = diseño horizontal (tab Archivos) | 'chip' = compacto (inline en mensaje)
    variant:     { type: String,  default: 'row' },
})

const emit = defineEmits(['delete'])

// Mapa de categoría → ícono de Material Symbols
const ICONS = {
    image:       'image',
    pdf:         'picture_as_pdf',
    spreadsheet: 'table_chart',
    document:    'description',
    file:        'attach_file',
}

const icon = ICONS[props.attachment.file_category] ?? 'attach_file'

// Colores por categoría de archivo
const COLOR = {
    image:       'text-violet-500',
    pdf:         'text-red-500',
    spreadsheet: 'text-emerald-600',
    document:    'text-blue-500',
    file:        'text-slate-400',
}

const iconColor = COLOR[props.attachment.file_category] ?? 'text-slate-400'
</script>

<template>

    <!-- ── Variante row: para la tab Archivos ─────────────────────────────── -->
    <div v-if="variant === 'row'"
        class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-colors group"
    >
        <!-- Ícono -->
        <span :class="['material-symbols-outlined text-2xl shrink-0 mt-0.5', iconColor]">
            {{ icon }}
        </span>

        <!-- Info -->
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">
                {{ attachment.filename }}
            </p>
            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                <span class="text-[10px] text-slate-400">{{ attachment.readable_size }}</span>
                <span v-if="attachment.user" class="text-[10px] text-slate-400">
                    · {{ attachment.user.name }}
                </span>
            </div>
            <!-- Descripción (solo en ticket_attachments) -->
            <p v-if="attachment.description"
                class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed"
            >
                {{ attachment.description }}
            </p>
        </div>

        <!-- Acciones -->
        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
            <!-- Descargar -->
            <a
                :href="attachment.download_url"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-brand hover:bg-brand/10 transition-colors"
                title="Descargar"
            >
                <span class="material-symbols-outlined text-[18px]">download</span>
            </a>
            <!-- Eliminar -->
            <button
                v-if="canDelete"
                type="button"
                class="flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-danger hover:bg-danger/10 transition-colors"
                title="Eliminar"
                @click="$emit('delete', attachment.id)"
            >
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
        </div>
    </div>

    <!-- ── Variante chip: adjunto inline dentro de un mensaje ─────────────── -->
    <a v-else
        :href="attachment.download_url"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 hover:border-brand hover:text-brand transition-colors max-w-[200px]"
        :title="attachment.filename"
    >
        <span :class="['material-symbols-outlined text-[14px] shrink-0', iconColor]">{{ icon }}</span>
        <span class="truncate">{{ attachment.filename }}</span>
        <span class="text-[10px] text-slate-400 shrink-0">{{ attachment.readable_size }}</span>
        <!-- Eliminar inline (solo si canDelete) -->
        <button
            v-if="canDelete"
            type="button"
            class="shrink-0 ml-0.5 text-slate-300 hover:text-danger transition-colors"
            title="Eliminar"
            @click.prevent="$emit('delete', attachment.id)"
        >
            <span class="material-symbols-outlined text-[13px]">close</span>
        </button>
    </a>

</template>
