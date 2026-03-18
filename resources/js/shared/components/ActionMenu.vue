<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
    actions: { type: Array, default: () => [] },
})

const open = ref(false)
const triggerEl = ref(null) // Referencia al botón
const menuEl = ref(null)    // Referencia al menú flotante
const menuStyle = ref({})   // Estilos de posición dinámica

const toggle = async () => {
    open.value = !open.value
    if (open.value) {
        await nextTick()
        calculatePosition()
    }
}

const calculatePosition = () => {
    if (!triggerEl.value || !menuEl.value) return

    const rect = triggerEl.value.getBoundingClientRect()

    // Calculamos la posición respecto a la ventana (viewport)
    // El menú aparecerá justo debajo del botón, alineado a la derecha
    menuStyle.value = {
        position: 'fixed',
        top: `${rect.bottom + 4}px`,
        left: `${rect.right - 180}px`, // 180px es el min-width definido
        width: '180px'
    }
}

const run = (action) => {
    open.value = false
    action.handler?.()
}

const onClickOutside = (e) => {
    // Si el menú está en el body, el trigger y el menú son elementos separados
    if (open.value &&
        !triggerEl.value.contains(e.target) &&
        !menuEl.value?.contains(e.target)) {
        open.value = false
    }
}

// Recalcular si se hace scroll o redimensiona la ventana
onMounted(() => {
    document.addEventListener('mousedown', onClickOutside)
    window.addEventListener('scroll', () => (open.value = false), true)
    window.addEventListener('resize', () => (open.value = false))
})

onUnmounted(() => {
    document.removeEventListener('mousedown', onClickOutside)
    window.removeEventListener('scroll', () => (open.value = false))
    window.removeEventListener('resize', () => (open.value = false))
})
</script>

<template>
    <div class="inline-block">
        <button
            ref="triggerEl"
            type="button"
            @click.stop="toggle"
            class="w-8 h-8 flex items-center justify-center rounded-lg
                   text-slate-400 hover:text-slate-600 dark:hover:text-slate-200
                   hover:bg-slate-100 dark:hover:bg-slate-700
                   transition-all focus:outline-none focus:ring-2 focus:ring-brand/30"
            :aria-expanded="open"
        >
            <span class="material-symbols-outlined text-[18px]">more_vert</span>
        </button>

        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="open"
                    ref="menuEl"
                    :style="menuStyle"
                    class="z-[9999] bg-white dark:bg-slate-800
                           rounded-xl border border-slate-200 dark:border-slate-700
                           shadow-xl py-1.5 origin-top-right"
                >
                    <template v-for="(action, i) in actions" :key="i">
                        <div v-if="action.separator" class="my-1 border-t border-slate-100 dark:border-slate-700" />

                        <button
                            v-else
                            type="button"
                            @click="run(action)"
                            class="w-full flex items-center gap-2.5 px-4 py-2 text-sm
                                   transition-colors text-left"
                            :class="action.variant === 'danger'
                                ? 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20'
                                : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'"
                        >
                            <span class="material-symbols-outlined text-[16px] shrink-0">
                                {{ action.icon }}
                            </span>
                            {{ action.label }}
                        </button>
                    </template>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
