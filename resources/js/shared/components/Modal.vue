<script setup>
/**
 * Modal.vue — Componente modal del sistema JVJ Technology
 *
 * Uso básico:
 *   <Modal :show="open" title="Editar contacto" @close="open = false">
 *     Contenido aquí
 *     <template #footer>
 *       <SecondaryButton @click="open = false">Cancelar</SecondaryButton>
 *       <PrimaryButton @click="save">Guardar</PrimaryButton>
 *     </template>
 *   </Modal>
 *
 * Props:
 *   show       Boolean  — controla visibilidad
 *   title      String   — título del header (se omite si se usa slot #header)
 *   size       String   — sm | md | lg | xl | 2xl | full (default: md)
 *   variant    String   — default | danger | warning | success (colorea el header)
 *   closable   Boolean  — permite cerrar con Esc y click en backdrop (default: true)
 *   scrollable Boolean  — el body del modal hace scroll internamente (default: true)
 *   footerAlign String  — left | center | between | end (default: end)
 *
 * Slots:
 *   default    — contenido principal
 *   #header    — reemplaza el header completo
 *   #icon      — ícono a la izquierda del título (Material Symbol name)
 *   #footer    — acciones del modal
 */
import { watch, onUnmounted } from 'vue';

const props = defineProps({
    show:        { type: Boolean, default: false },
    title:       { type: String,  default: '' },
    size:        { type: String,  default: 'md' },    // sm | md | lg | xl | 2xl | full
    variant:     { type: String,  default: 'default' }, // default | danger | warning | success
    closable:    { type: Boolean, default: true },
    scrollable:  { type: Boolean, default: true },
    footerAlign: { type: String,  default: 'end' },   // left | center | between | end
});

const emit = defineEmits(['close']);

const close = () => {
    if (props.closable) emit('close');
};

// Bloquear scroll del body mientras el modal está abierto
watch(() => props.show, (visible) => {
    document.body.style.overflow = visible ? 'hidden' : '';
}, { immediate: true });

// Cerrar con Escape
const onKey = (e) => {
    if (e.key === 'Escape' && props.show) close();
};
document.addEventListener('keydown', onKey);
onUnmounted(() => {
    document.removeEventListener('keydown', onKey);
    document.body.style.overflow = ''; // Limpiar por si el componente se desmonta abierto
});

// Variante → colores del header y del ícono de cierre
const variantStyles = {
    default: {
        header: 'border-b border-slate-200 dark:border-slate-700',
        title:  'text-slate-900 dark:text-white',
        icon:   'text-brand',
        close:  'text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:text-slate-300 dark:hover:bg-slate-700',
    },
    danger: {
        header: 'border-b border-red-100 dark:border-red-900/50 bg-red-50 dark:bg-red-900/20',
        title:  'text-red-700 dark:text-red-300',
        icon:   'text-danger',
        close:  'text-red-400 hover:text-red-600 hover:bg-red-100 dark:hover:bg-red-900/40',
    },
    warning: {
        header: 'border-b border-amber-100 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-900/20',
        title:  'text-amber-700 dark:text-amber-300',
        icon:   'text-warning',
        close:  'text-amber-400 hover:text-amber-600 hover:bg-amber-100 dark:hover:bg-amber-900/40',
    },
    success: {
        header: 'border-b border-emerald-100 dark:border-emerald-900/50 bg-emerald-50 dark:bg-emerald-900/20',
        title:  'text-emerald-700 dark:text-emerald-300',
        icon:   'text-success',
        close:  'text-emerald-400 hover:text-emerald-600 hover:bg-emerald-100 dark:hover:bg-emerald-900/40',
    },
};

const footerAlignClass = {
    left:    'justify-start',
    center:  'justify-center',
    between: 'justify-between',
    end:     'justify-end',
};
</script>

<template>
    <Teleport to="body">
        <!-- Backdrop fade -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                role="dialog"
                aria-modal="true"
                :aria-label="title || 'Modal'"
            >
                <!-- Backdrop -->
                <div
                    class="absolute inset-0 bg-slate-900/50 dark:bg-slate-950/70 backdrop-blur-sm"
                    @click="close"
                    aria-hidden="true"
                />

                <!-- Panel scale-in -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-3"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-2"
                >
                    <div
                        v-if="show"
                        :class="[
                            'relative z-10 flex flex-col w-full',
                            'bg-white dark:bg-slate-800',
                            'rounded-2xl shadow-2xl shadow-slate-900/25 dark:shadow-slate-950/60',
                            'border border-slate-200 dark:border-slate-700',
                            'max-h-[90vh]',
                            // Tamaños
                            size === 'sm'   && 'max-w-sm',
                            size === 'md'   && 'max-w-lg',
                            size === 'lg'   && 'max-w-2xl',
                            size === 'xl'   && 'max-w-4xl',
                            size === '2xl'  && 'max-w-6xl',
                            size === 'full' && 'max-w-full h-[90vh]',
                        ]"
                    >
                        <!-- ── Header ──────────────────────────────────────────── -->
                        <div
                            v-if="title || $slots.header || $slots.icon || closable"
                            :class="[
                                'flex items-center justify-between gap-3 px-6 py-4 shrink-0 rounded-t-2xl',
                                variantStyles[variant]?.header,
                            ]"
                        >
                            <!-- Slot header completo (reemplaza todo) -->
                            <slot name="header">
                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- Slot icon: ícono opcional a la izquierda del título -->
                                    <slot name="icon">
                                        <!-- Si no se pasa slot icon, no renderiza nada -->
                                    </slot>

                                    <h2
                                        :class="[
                                            'text-base font-bold truncate',
                                            variantStyles[variant]?.title,
                                        ]"
                                    >
                                        {{ title }}
                                    </h2>
                                </div>
                            </slot>

                            <!-- Botón cerrar -->
                            <button
                                v-if="closable"
                                @click="close"
                                type="button"
                                :class="[
                                    'shrink-0 ml-2 p-1.5 rounded-lg transition-colors',
                                    variantStyles[variant]?.close,
                                ]"
                                aria-label="Cerrar modal"
                            >
                                <span class="material-symbols-outlined text-xl leading-none">close</span>
                            </button>
                        </div>

                        <!-- ── Body ───────────────────────────────────────────── -->
                        <div
                            :class="[
                                'flex-1 px-6 py-5',
                                scrollable ? 'overflow-y-auto' : 'overflow-visible',
                            ]"
                        >
                            <slot />
                        </div>

                        <!-- ── Footer ─────────────────────────────────────────── -->
                        <div
                            v-if="$slots.footer"
                            :class="[
                                'flex items-center flex-wrap gap-3 px-6 py-4',
                                'border-t border-slate-200 dark:border-slate-700 shrink-0',
                                footerAlignClass[footerAlign],
                            ]"
                        >
                            <slot name="footer" />
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
