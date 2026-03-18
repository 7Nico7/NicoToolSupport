<script setup>
// InputLabel.vue
defineProps({
    value:    { type: String, default: '' },
    required: { type: Boolean, default: false },
    for:      { type: String, default: null },
});
</script>

<template>
    <label
        :for="for"
        class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5"
    >
        <slot>{{ value }}</slot>
        <span v-if="required" class="text-danger ml-0.5">*</span>
    </label>
</template>
