<script setup>
// resources/js/features/Kanban/KanbanColumn.vue
import { computed } from 'vue';
import draggable from 'vuedraggable';
import KanbanCard from '@/features/Kanban/KanbanCard.vue';
import { useKanbanStore } from '@/features/Kanban/stores/kanbanStore';

const props = defineProps({
    status: { type: Object, required: true },
});

const emit  = defineEmits(['edit-ticket']);
const store = useKanbanStore();

const tickets = computed(() => store.ticketsByStatus[String(props.status.id)] ?? []);

// vuedraggable necesita un v-model local para el grupo compartido
// usamos un setter que llama a moveTicket en el store
const localTickets = computed({
    get: () => tickets.value,
    set: () => {}, // El movimiento real se hace en onEnd
});

const onEnd = async (evt) => {
    const ticketId    = parseInt(evt.item.dataset.id);
    const newStatusId = parseInt(evt.to.dataset.statusId);
    const oldStatusId = parseInt(evt.from.dataset.statusId);

    if (newStatusId !== oldStatusId) {
        await store.moveTicket(ticketId, newStatusId);
    }
};
</script>

<template>
    <div
        class="flex flex-col w-72 shrink-0 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50 overflow-hidden max-h-[calc(100vh-11rem)]"
    >
        <!-- Column Header -->
        <div
            class="flex items-center justify-between px-4 py-3 shrink-0"
            :style="{ borderBottom: `3px solid ${status.color}` }"
        >
            <div class="flex items-center gap-2">
                <div class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: status.color }" />
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">{{ status.name }}</h3>
            </div>
            <span
                class="inline-flex items-center justify-center h-5 min-w-5 px-1.5 rounded-full text-[11px] font-black text-white"
                :style="{ backgroundColor: status.color }"
            >
                {{ tickets.length }}
            </span>
        </div>

        <!-- Ticket list -->
        <div class="flex-1 overflow-y-auto p-3 custom-scrollbar">
            <draggable
                v-model="localTickets"
                group="kanban-tickets"
                item-key="id"
                :data-status-id="status.id"
                ghost-class="kanban-ghost"
                chosen-class="kanban-chosen"
                drag-class="kanban-drag"
                :animation="150"
                @end="onEnd"
                class="space-y-2.5 min-h-[120px]"
            >
                <template #item="{ element }">
                    <KanbanCard
                        :data-id="element.id"
                        :ticket="element"
                        :status-color="status.color"
                        @edit="$emit('edit-ticket', $event)"
                    />
                </template>
                <template #footer>
                    <div
                        v-if="tickets.length === 0"
                        class="flex flex-col items-center justify-center h-24 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 text-slate-400"
                    >
                        <span class="material-symbols-outlined text-2xl mb-1">inbox</span>
                        <p class="text-xs">Sin tickets</p>
                    </div>
                </template>
            </draggable>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 3px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-200 dark:bg-slate-600 rounded-full;
}
:deep(.kanban-ghost)  { opacity: 0.3; }
:deep(.kanban-chosen) { transform: rotate(1deg); box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15); }
:deep(.kanban-drag)   { transform: rotate(2deg); box-shadow: 0 20px 40px -10px rgb(0 0 0 / 0.25); opacity: 0.9; }
</style>
