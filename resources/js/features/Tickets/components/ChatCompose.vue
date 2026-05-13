<script setup>
// resources/js/features/tickets/components/ChatCompose.vue
import { ref } from 'vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import Checkbox from '@/shared/components/Checkbox.vue'

const props = defineProps({
    canInternal: { type: Boolean, default: false },
    sending:     { type: Boolean, default: false },
})

const emit = defineEmits(['send'])

const newMessage   = ref('')
const isInternal   = ref(false)
const pendingFiles = ref([])
const fileInputRef = ref(null)

const canSend = () => newMessage.value.trim().length > 0 || pendingFiles.value.length > 0

const formatSize = (bytes) => {
    if (bytes >= 1_048_576) return `${(bytes / 1_048_576).toFixed(1)} MB`
    if (bytes >= 1_024)     return `${Math.round(bytes / 1_024)} KB`
    return `${bytes} B`
}

const onFilesSelected = (e) => {
    pendingFiles.value = [...pendingFiles.value, ...Array.from(e.target.files ?? [])]
    e.target.value = ''
}

const removePendingFile = (i) => {
    pendingFiles.value = pendingFiles.value.filter((_, idx) => idx !== i)
}

const isImage = (file) => file.type.startsWith('image/')
const previewUrl = (file) => URL.createObjectURL(file)

const send = () => {
    if (!canSend() || props.sending) return
    emit('send', {
        message:     newMessage.value.trim(),
        is_internal: isInternal.value,
        files:       [...pendingFiles.value],
        onDone: () => {
            newMessage.value   = ''
            pendingFiles.value = []
            isInternal.value   = false
        },
        onError: () => {},
    })
}
</script>

<template>
    <div :class="[
        'shrink-0 border-t bg-white dark:bg-slate-900 p-4 transition-colors',
        isInternal
            ? 'border-amber-300 dark:border-amber-700 bg-amber-50/50 dark:bg-amber-900/10'
            : 'border-slate-200 dark:border-slate-700',
    ]">

        <!-- Preview archivos pendientes -->
        <div v-if="pendingFiles.length > 0" class="flex flex-wrap gap-2 mb-3">
            <div v-for="(file, i) in pendingFiles" :key="i" class="relative group">
                <div v-if="isImage(file)" class="w-16 h-12 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700">
                    <img :src="previewUrl(file)" class="w-full h-full object-cover" :alt="file.name" />
                </div>
                <div v-else class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg bg-brand/10 border border-brand/20 text-[10px] font-semibold text-brand max-w-[160px]">
                    <span class="material-symbols-outlined text-[13px]">attach_file</span>
                    <span class="truncate">{{ file.name }}</span>
                    <span class="text-brand/60 shrink-0">{{ formatSize(file.size) }}</span>
                </div>
                <button type="button"
                    class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-slate-700 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    @click="removePendingFile(i)"
                >
                    <span class="material-symbols-outlined text-[10px]">close</span>
                </button>
            </div>
        </div>

        <!-- Textarea + barra de herramientas -->
        <div :class="[
            'rounded-2xl border transition-colors',
            isInternal ? 'border-amber-300 dark:border-amber-700' : 'border-slate-200 dark:border-slate-700 focus-within:border-brand',
        ]">
            <textarea
                v-model="newMessage"
                :placeholder="isInternal ? 'Nota interna — solo visible para el equipo...' : 'Escribe tu respuesta...'"
                rows="3"
                class="w-full px-4 py-3 text-sm bg-transparent text-slate-900 dark:text-white placeholder-slate-400 resize-none focus:outline-none rounded-t-2xl"
                @keydown.ctrl.enter="send"
            />

            <div class="flex items-center justify-between px-3 py-2 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <input ref="fileInputRef" type="file" multiple
                        accept="image/*,.pdf,.xlsx,.xls,.docx,.doc,.csv,.zip"
                        class="hidden" @change="onFilesSelected"
                    />
                    <button type="button"
                        class="flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-brand hover:bg-brand/10 transition-colors"
                        title="Adjuntar archivo"
                        @click="fileInputRef?.click()"
                    >
                        <span class="material-symbols-outlined text-[18px]">attach_file</span>
                    </button>

                    <button
                        v-if="canInternal"
                        type="button"
                        :class="[
                            'flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold transition-colors',
                            isInternal
                                ? 'bg-amber-200 dark:bg-amber-800 text-amber-800 dark:text-amber-200'
                                : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-600',
                        ]"
                        @click="isInternal = !isInternal"
                    >
                        <span class="material-symbols-outlined text-[14px]">lock</span>
                        Nota interna
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-[10px] text-slate-400 hidden sm:block">Ctrl+Enter para enviar</span>
                    <PrimaryButton
                        size="sm"
                        icon="send"
                        :loading="sending"
                        :disabled="!canSend()"
                        @click="send"
                    >
                        {{ isInternal ? 'Añadir nota' : 'Enviar' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>
