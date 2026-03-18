<script setup>
defineProps({
    modelValue: { type: Boolean, default: false },
    disabled:   { type: Boolean, default: false },
    label:      { type: String,  default: '' },
    id:         { type: String,  default: null },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <label
        :class="['inline-flex items-center gap-2.5 cursor-pointer select-none', disabled && 'opacity-50 cursor-not-allowed']"
    >
        <div class="relative flex items-center">
            <input
                :id="id"
                type="checkbox"
                :checked="modelValue"
                :disabled="disabled"
                @change="emit('update:modelValue', $event.target.checked)"
                class="sr-only"
            />
            <!-- Custom checkbox -->
            <div
                :class="[
                    'h-4.5 w-4.5 rounded-md border-2 flex items-center justify-center transition-all duration-150',
                    modelValue
                        ? 'bg-brand border-brand'
                        : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 hover:border-brand',
                ]"
                style="width: 1.125rem; height: 1.125rem;"
            >
                <svg
                    v-if="modelValue"
                    class="w-2.5 h-2.5 text-white"
                    fill="none"
                    viewBox="0 0 10 8"
                >
                    <path d="M1 4l3 3 5-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <span v-if="label || $slots.default" class="text-sm text-slate-700 dark:text-slate-300">
            <slot>{{ label }}</slot>
        </span>
    </label>
</template>
