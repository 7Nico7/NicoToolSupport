<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    align:  { type: String, default: 'right' }, // left | right
    width:  { type: String, default: '48' },    // tailwind w-* value
});

const open = ref(false);

const close = () => { open.value = false; };
const toggle = () => { open.value = !open.value; };

// Close on click outside
const dropdownRef = ref(null);
const handler = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        close();
    }
};
onMounted(() => document.addEventListener('click', handler));
onUnmounted(() => document.removeEventListener('click', handler));
</script>

<template>
    <div ref="dropdownRef" class="relative inline-block">
        <!-- Trigger -->
        <div @click.stop="toggle">
            <slot name="trigger" />
        </div>

        <!-- Panel -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95 -translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="open"
                @click="close"
                :class="[
                    'absolute z-50 mt-2 rounded-xl border border-slate-200 dark:border-slate-700',
                    'bg-white dark:bg-slate-800 shadow-xl shadow-slate-900/10 dark:shadow-slate-900/40',
                    'py-1 overflow-hidden',
                    `w-${width}`,
                    align === 'right' ? 'right-0' : 'left-0',
                ]"
            >
                <slot name="content" /><slot />
            </div>
        </Transition>
    </div>
</template>
