<script setup>
// resources/js/features/Kanban/AgentSearchInput.vue
import { ref, watch } from 'vue';
import { useAgentSearch } from '@/features/Kanban/composables/useAgentSearch';
import TextInput from '@/shared/components/TextInput.vue';

const props = defineProps({
    modelValue:  { type: [Number, null], default: null },
    initialName: { type: String,         default: '' },
    placeholder: { type: String,         default: 'Buscar agente...' },
});
const emit = defineEmits(['update:modelValue']);

const { results, isSearching, search, clear } = useAgentSearch();
const query       = ref(props.initialName);
const showResults = ref(false);

watch(() => props.initialName, (v) => { query.value = v; });

const onInput = async (value) => {
    query.value = value;
    emit('update:modelValue', null);
    await search(value);
    showResults.value = results.value.length > 0;
};

const select = (agent) => {
    query.value = agent.name;
    emit('update:modelValue', agent.id);
    showResults.value = false;
    clear();
};

const clearSelection = () => {
    query.value = '';
    emit('update:modelValue', null);
    clear();
    showResults.value = false;
};

const onBlur = () => setTimeout(() => { showResults.value = false; }, 150);
</script>

<template>
    <div class="relative" @blur.capture="onBlur">
        <TextInput
            :model-value="query"
            :placeholder="placeholder"
            :icon="isSearching ? 'progress_activity' : 'person_search'"
            :icon-trail="query ? 'close' : null"
            autocomplete="off"
            @update:model-value="onInput"
            @trailing-click="clearSelection"
        />

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
        >
            <ul
                v-if="showResults"
                class="absolute z-50 top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden max-h-48 overflow-y-auto"
            >
                <li
                    v-for="agent in results"
                    :key="agent.id"
                    @mousedown="select(agent)"
                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer transition-colors"
                >
                    <div class="h-7 w-7 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-xs font-bold shrink-0">
                        {{ agent.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ agent.name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ agent.role }}</p>
                    </div>
                </li>
            </ul>
        </Transition>
    </div>
</template>
