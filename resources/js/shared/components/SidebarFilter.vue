<script setup>
/**
 * SidebarFilter.vue — Panel de filtros lateral deslizable
 *
 * Uso:
 *   <SidebarFilter v-model:open="showFilters" :active-count="3" @clear="clearAll">
 *     <!-- campos de filtro aquí -->
 *   </SidebarFilter>
 *
 * Props:
 *   open         Boolean  — controla si el sidebar está visible (v-model:open)
 *   activeCount  Number   — número de filtros activos (muestra badge)
 *   title        String   — título del panel (default: "Filtros")
 *   side         String   — 'left' | 'right' (default: 'right')
 *   width        String   — ancho del panel (default: '320px')
 *
 * Emits:
 *   update:open  — para v-model:open
 *   clear        — cuando el usuario pulsa "Limpiar filtros"
 *
 * El botón que abre el sidebar va FUERA de este componente (en la página padre),
 * usando el slot #trigger o un botón manual con @click="open = true".
 */
import { watch } from 'vue';
import DangerButton from '@/shared/components/buttons/DangerButton.vue';

const props = defineProps({
    open:        { type: Boolean, default: false },
    activeCount: { type: Number,  default: 0 },
    title:       { type: String,  default: 'Filtros' },
    side:        { type: String,  default: 'right' },  // 'left' | 'right'
    width:       { type: String,  default: '320px' },
});

const emit = defineEmits(['update:open', 'clear']);

const close = () => emit('update:open', false);

// Bloquear scroll del body mientras el sidebar está abierto
watch(() => props.open, (v) => {
    document.body.style.overflow = v ? 'hidden' : '';
});
</script>

<template>
    <Teleport to="body">
        <!-- Overlay oscuro -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
              v-show="open"
                class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm"
                @click="close"
                aria-hidden="true"
            />
        </Transition>

        <!-- Panel deslizable -->
        <Transition
            :enter-active-class="`transition duration-300 ease-out`"
            :enter-from-class="side === 'right' ? 'translate-x-full' : '-translate-x-full'"
            enter-to-class="translate-x-0"
            :leave-active-class="`transition duration-250 ease-in`"
            leave-from-class="translate-x-0"
            :leave-to-class="side === 'right' ? 'translate-x-full' : '-translate-x-full'"
        >
            <div
                v-if="open"
                :class="[
                    'fixed top-0 bottom-0 z-50 flex flex-col',
                    'bg-white dark:bg-slate-800',
                    'border-slate-200 dark:border-slate-700',
                    'shadow-2xl shadow-slate-900/20',
                    side === 'right' ? 'right-0 border-l' : 'left-0 border-r',
                ]"
                :style="{ width }"
                role="dialog"
                aria-modal="true"
                :aria-label="title"
            >
                <!-- ── Header ───────────────────────────────────────────── -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-700 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-base text-slate-500">filter_alt</span>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white">{{ title }}</h2>
                        <!-- Badge de filtros activos -->
                        <Transition
                            enter-active-class="transition duration-150 scale-0"
                            enter-to-class="scale-100"
                        >
                            <span
                                v-if="activeCount > 0"
                                class="inline-flex items-center justify-center h-5 min-w-5 px-1.5 rounded-full text-[10px] font-black bg-brand text-white"
                            >{{ activeCount }}</span>
                        </Transition>
                    </div>
                    <button
                        @click="close"
                        type="button"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                        aria-label="Cerrar filtros"
                    >
                        <span class="material-symbols-outlined text-xl leading-none">close</span>
                    </button>
                </div>

                <!-- ── Cuerpo con scroll ────────────────────────────────── -->
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">
                    <slot />
                </div>

                <!-- ── Footer ──────────────────────────────────────────── -->
                <div class="shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3">
                    <DangerButton
                        v-if="activeCount > 0"
                        variant="ghost"
                        size="sm"
                        icon="filter_alt_off"
                        @click="emit('clear')"
                    >
                        Limpiar ({{ activeCount }})
                    </DangerButton>
                    <span v-else class="text-xs text-slate-400">Sin filtros activos</span>

                    <button
                        @click="close"
                        type="button"
                        class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
