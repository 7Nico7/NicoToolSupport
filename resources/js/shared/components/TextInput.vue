<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    type:       { type: String, default: 'text' },
    placeholder:{ type: String, default: '' },
    disabled:   { type: Boolean, default: false },
    autofocus:  { type: Boolean, default: false },
    error:      { type: Boolean, default: false },
    icon:       { type: String, default: null },   // material symbol name (leading)
    iconTrail:  { type: String, default: null },   // material symbol name (trailing)
});

const emit = defineEmits(['update:modelValue', 'trailing-click']);
const inputRef = ref(null);

onMounted(() => {
    if (props.autofocus) inputRef.value?.focus();
});

defineExpose({ focus: () => inputRef.value?.focus() });
</script>

<template>
    <div class="relative flex items-center">
        <!-- Leading icon -->
        <span
            v-if="icon"
            class="absolute left-3 material-symbols-outlined text-lg text-slate-400 dark:text-slate-500 pointer-events-none select-none"
        >{{ icon }}</span>

        <input
            ref="inputRef"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            @input="emit('update:modelValue', $event.target.value)"
            :class="[
                'w-full py-2 text-sm rounded-xl border transition-all duration-150',
                'bg-white dark:bg-slate-800/60',
                'text-slate-900 dark:text-white',
                'placeholder-slate-400 dark:placeholder-slate-500',
                'focus:outline-none focus:ring-2',
                icon        ? 'pl-9'  : 'pl-3',
                iconTrail   ? 'pr-9'  : 'pr-3',
                error
                    ? 'border-danger focus:ring-danger/30 focus:border-danger'
                    : 'border-slate-200 dark:border-slate-700 focus:ring-brand/30 focus:border-brand',
                disabled ? 'opacity-50 cursor-not-allowed bg-slate-50 dark:bg-slate-900' : '',
            ]"
        />

        <!-- Trailing icon -->
        <button
            v-if="iconTrail"
            type="button"
            @click="emit('trailing-click')"
            class="absolute right-2.5 material-symbols-outlined text-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
        >{{ iconTrail }}</button>
    </div>
</template>
