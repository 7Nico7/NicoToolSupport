<script setup>
// resources/js/shared/components/ImagePreview.vue
//
// Muestra una imagen como thumbnail con lightbox al hacer click.
// Reutilizable en cualquier módulo (chat, kanban, gantt).
//
// Props:
//   src        — URL de la imagen (puede ser download_url firmada)
//   alt        — texto alternativo
//   filename   — nombre del archivo para el lightbox
//   size       — 'sm' (80px) | 'md' (128px) | 'lg' (192px)
//   canDelete  — muestra botón X en la esquina
//
// Emits:
//   delete — cuando el usuario hace click en eliminar

import { ref } from 'vue'

const props = defineProps({
    src:       { type: String,  required: true },
    alt:       { type: String,  default: 'Imagen adjunta' },
    filename:  { type: String,  default: '' },
    size:      { type: String,  default: 'md' },
    canDelete: { type: Boolean, default: false },
})

const emit = defineEmits(['delete'])

const lightboxOpen = ref(false)

const sizeClass = {
    sm: 'w-20 h-16',
    md: 'w-32 h-24',
    lg: 'w-48 h-36',
}[props.size] ?? 'w-32 h-24'
</script>

<template>
    <!-- Thumbnail -->
    <div :class="['relative group rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0', sizeClass]">
        <img
            :src="src"
            :alt="alt"
            class="w-full h-full object-cover cursor-zoom-in"
            @click="lightboxOpen = true"
            loading="lazy"
        />

        <!-- Overlay en hover -->
        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5">
            <!-- Abrir en nueva pestaña -->
            <a
                :href="src"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center justify-center w-7 h-7 rounded-lg bg-white/20 hover:bg-white/40 transition-colors"
                title="Descargar"
                @click.stop
            >
                <span class="material-symbols-outlined text-white text-[16px]">download</span>
            </a>
            <!-- Ampliar -->
            <button
                type="button"
                class="flex items-center justify-center w-7 h-7 rounded-lg bg-white/20 hover:bg-white/40 transition-colors"
                title="Ampliar"
                @click.stop="lightboxOpen = true"
            >
                <span class="material-symbols-outlined text-white text-[16px]">zoom_in</span>
            </button>
        </div>

        <!-- Botón eliminar en esquina -->
        <button
            v-if="canDelete"
            type="button"
            class="absolute top-1 right-1 w-5 h-5 rounded-full bg-black/60 hover:bg-danger flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all"
            title="Eliminar"
            @click.stop="$emit('delete')"
        >
            <span class="material-symbols-outlined text-white text-[12px]">close</span>
        </button>
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="lightboxOpen"
                class="fixed inset-0 z-[100] bg-black/90 flex flex-col items-center justify-center p-4"
                @click.self="lightboxOpen = false"
            >
                <!-- Header del lightbox -->
                <div class="absolute top-4 left-0 right-0 flex items-center justify-between px-6">
                    <span class="text-sm font-semibold text-white/80 truncate max-w-[60%]">{{ filename }}</span>
                    <div class="flex items-center gap-2">
                        <a
                            :href="src"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition-colors"
                        >
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            Descargar
                        </a>
                        <button
                            type="button"
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 transition-colors"
                            @click="lightboxOpen = false"
                        >
                            <span class="material-symbols-outlined text-white text-[20px]">close</span>
                        </button>
                    </div>
                </div>

                <!-- Imagen -->
                <img
                    :src="src"
                    :alt="alt"
                    class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl"
                    @click.stop
                />
            </div>
        </Transition>
    </Teleport>
</template>
