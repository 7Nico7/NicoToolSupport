<script setup>
// resources/js/features/tickets/components/TicketListItem.vue
import { computed } from 'vue'

const props = defineProps({
    ticket:   { type: Object,  required: true },
    isActive: { type: Boolean, default: false },
})

const emit = defineEmits(['select'])

const timeAgo = computed(() => {
    const diff  = Date.now() - new Date(props.ticket.updated_at).getTime()
    const mins  = Math.floor(diff / 60000)
    const hours = Math.floor(diff / 3600000)
    const days  = Math.floor(diff / 86400000)
    if (mins  < 1)  return 'Ahora'
    if (mins  < 60) return `${mins}m`
    if (hours < 24) return `${hours}h`
    return `${days}d`
})
</script>

<template>
    <button
        type="button"
        class="w-full text-left p-4 border-b border-slate-100 dark:border-slate-800 transition-colors relative"
        :class="isActive
            ? 'bg-brand/5 dark:bg-brand/10 border-l-4 border-l-brand'
            : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 border-l-4 border-l-transparent'"
        @click="emit('select', ticket.id)"
    >
        <!-- Número + estado + tiempo -->
        <div class="flex items-center justify-between mb-1.5">
            <span :class="['text-[10px] font-black uppercase tracking-wider', isActive ? 'text-brand' : 'text-slate-400']">
                #{{ ticket.ticket_number }}
            </span>
            <div class="flex items-center gap-1.5">
                <span
                    v-if="ticket.status"
                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide"
                    :style="{ backgroundColor: ticket.status.color + '20', color: ticket.status.color }"
                >
                    {{ ticket.status.name }}
                </span>
                <span class="text-[10px] text-slate-400 shrink-0">{{ timeAgo }}</span>
            </div>
        </div>

        <!-- Título -->
        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate mb-0.5">
            {{ ticket.title }}
        </p>

        <!-- Meta -->
        <div class="flex items-center justify-between">
            <span class="text-[11px] text-slate-500 truncate">{{ ticket.created_by_name ?? '—' }}</span>
            <span v-if="ticket.messages_count > 0" class="flex items-center gap-1 text-[10px] text-slate-400 shrink-0">
                <span class="material-symbols-outlined text-[12px]">chat_bubble</span>
                {{ ticket.messages_count }}
            </span>
        </div>

        <!-- Dot de prioridad -->
        <div
            v-if="ticket.priority"
            class="absolute top-4 right-4 w-2 h-2 rounded-full shrink-0"
            :style="{ backgroundColor: ticket.priority.color }"
            :title="ticket.priority.name"
        />
    </button>
</template>
