<script setup>
import InputLabel from '@/shared/components/InputLabel.vue'

const model = defineModel()

const props = defineProps({
    label: String,
    options: {
        type: Array,
        default: () => []
    },
    placeholder: {
        type: String,
        default: 'Seleccionar...'
    },
    required: {
        type: Boolean,
        default: false
    },
    optionValue: {
        type: String,
        default: 'id'
    },
    optionLabel: {
        type: String,
        default: 'name'
    }
})
</script>

<template>
    <div>
        <InputLabel v-if="label" :value="label" :required="required" />

        <select v-model="model"
            class="mt-1 w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
            <option :value="null" disabled>
                {{ placeholder }}
            </option>

            <option v-for="item in options" :key="item[optionValue]" :value="item[optionValue]">
                {{ item[optionLabel] }}
            </option>

        </select>
    </div>
</template>
