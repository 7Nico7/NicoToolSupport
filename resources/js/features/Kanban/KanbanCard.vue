<script setup>
// resources/js/features/Kanban/KanbanCard.vue
import { computed } from 'vue';

const props = defineProps({
    ticket:      { type: Object, required: true },
    statusColor: { type: String, default: '#2563eb' },
});

const emit = defineEmits(['edit']);

// ── Computed helpers ──────────────────────────────────────────────────────────

const priorityConfig = computed(() => {
    const p = props.ticket.priority;
    if (!p) return null;
    return { name: p.name, color: p.color ?? '#94a3b8' };
});

const isDueOverdue = computed(() => {
    if (!props.ticket.due_date) return false;
    return new Date(props.ticket.due_date) < new Date();
});

const isDueSoon = computed(() => {
    if (!props.ticket.due_date || isDueOverdue.value) return false;
    const diff = new Date(props.ticket.due_date) - new Date();
    return diff < 1000 * 60 * 60 * 24 * 2; // < 2 días
});

const formatDueDate = computed(() => {
    if (!props.ticket.due_date) return null;
    return new Date(props.ticket.due_date).toLocaleDateString('es-MX', {
        day: '2-digit', month: 'short',
    });
});

const messageCount = computed(() =>
    props.ticket.messages_count ?? props.ticket.messages?.length ?? 0
);
</script>

<template>
    <div
        v-bind="$attrs"
        class="group relative bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-grab active:cursor-grabbing overflow-hidden"
    >
        <!-- Status color bar -->
        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl" :style="{ backgroundColor: statusColor }" />

        <div class="pl-4 pr-3 pt-3 pb-3">
            <!-- Header row: ticket number + edit button -->
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex flex-col gap-0.5 flex-1 min-w-0">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        {{ ticket.ticket_number }}
                    </span>
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white leading-snug truncate">
                        {{ ticket.title }}
                    </h4>
                </div>
                <button
                    @click.stop="$emit('edit', ticket.id)"
                    type="button"
                    class="shrink-0 p-1 rounded-lg text-slate-300 hover:text-brand hover:bg-blue-50 dark:hover:bg-brand/10 opacity-0 group-hover:opacity-100 transition-all"
                >
                    <span class="material-symbols-outlined text-base">open_in_new</span>
                </button>
            </div>

            <!-- Tags row: priority + type + category -->
            <div class="flex items-center gap-1.5 flex-wrap mb-2">
                <span
                    v-if="priorityConfig"
                    class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide text-white"
                    :style="{ backgroundColor: priorityConfig.color }"
                >
                    {{ priorityConfig.name }}
                </span>
                <span
                    v-if="ticket.type"
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300"
                >
                    {{ ticket.type.name }}
                </span>
                <span
                    v-if="ticket.category"
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300"
                >
                    {{ ticket.category.name }}
                </span>
            </div>

            <!-- Assignee -->
            <div v-if="ticket.assigned_user" class="flex items-center gap-1.5 mb-2">
                <div class="h-5 w-5 rounded-md bg-brand/10 text-brand flex items-center justify-center text-[10px] font-bold shrink-0">
                    {{ ticket.assigned_user.name.charAt(0).toUpperCase() }}
                </div>
                <span class="text-xs text-slate-500 dark:text-slate-400 truncate">
                    {{ ticket.assigned_user.name }}
                </span>
            </div>

            <!-- Footer: due date + messages -->
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 dark:border-slate-700">
                <div
                    v-if="formatDueDate"
                    class="flex items-center gap-1 text-[10px] font-semibold"
                    :class="{
                        'text-danger': isDueOverdue,
                        'text-warning': isDueSoon && !isDueOverdue,
                        'text-slate-400': !isDueOverdue && !isDueSoon,
                    }"
                >
                    <span class="material-symbols-outlined text-xs">schedule</span>
                    {{ formatDueDate }}
                    <span v-if="isDueOverdue">(vencido)</span>
                </div>
                <div v-else />

                <div v-if="messageCount > 0" class="flex items-center gap-1 text-[10px] text-slate-400">
                    <span class="material-symbols-outlined text-xs">chat_bubble</span>
                    {{ messageCount }}
                </div>
            </div>
        </div>
    </div>
</template>
