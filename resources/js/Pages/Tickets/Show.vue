<script setup>
// resources/js/Pages/Tickets/Show.vue
//
// Página de chat de tickets — 3 columnas.
// Toda la lógica de negocio vive en useTicketChat.
// Esta página solo ensambla componentes y pasa datos.

import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TextInput from '@/shared/components/TextInput.vue'
import TicketListItem  from '@/features/Tickets/components/TicketListItem.vue'
import ChatMessage     from '@/features/Tickets/components/ChatMessage.vue'
import ChatCompose     from '@/features/Tickets/components/ChatCompose.vue'
import TicketInfoPanel from '@/features/Tickets/components/TicketInfoPanel.vue'
import { useTicketChat } from '@/features/Tickets/composables/useTicketChat'

const props = defineProps({
    tickets:      { type: Array,  default: () => [] },
    activeTicket: { type: Object, default: null },
    statuses:     { type: Array,  default: () => [] },
    can:          { type: Object, default: () => ({ create: false, delete: false, internal: false }) },
})

const {
    // Estado
    currentTicket, loadingTicket, sending, uploadingFile,
    search, threadEl,
    // Computed
    filteredTickets, sortedMessages, canDeleteAttachments, authUser,
    // Acciones
    selectTicket,
    handleSend,
    handleUploadAttachment,
    handleDeleteAttachment,
    handleDeleteMessageAttachment,
} = useTicketChat(props)
</script>

<template>
    <Head title="Chat de Tickets" />

    <AuthenticatedLayout :full-height="true">

        <!--
            Clave del scroll correcto — 3 reglas de flexbox:
            1. Contenedor padre: h-full + overflow-hidden (techo fijo para todos los hijos)
            2. Cada columna: flex flex-col + overflow-hidden (techo para sus propios hijos)
            3. El hijo scrollable: flex-1 + min-h-0 + overflow-y-auto
               min-h-0 anula el `min-height: auto` predeterminado de los flex items,
               que de otra forma impide que overflow-y-auto funcione.
        -->
        <div class="flex h-full overflow-hidden">

            <!-- ── Columna izquierda: lista de tickets ──────────────────────── -->
            <aside class="w-72 shrink-0 flex flex-col overflow-hidden bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700">

                <!-- Cabecera fija -->
                <div class="shrink-0 p-4 border-b border-slate-200 dark:border-slate-700">
                    <h1 class="text-lg font-black text-slate-900 dark:text-white tracking-tight mb-3">
                        Tickets
                    </h1>
                    <TextInput v-model="search" icon="search" placeholder="Buscar ticket..." />
                </div>

                <!-- Lista scrollable — min-h-0 es obligatorio aquí -->
                <nav class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">
                    <TicketListItem
                        v-for="ticket in filteredTickets"
                        :key="ticket.id"
                        :ticket="ticket"
                        :is-active="currentTicket?.id === ticket.id"
                        @select="selectTicket"
                    />
                    <div v-if="filteredTickets.length === 0"
                        class="flex flex-col items-center py-12 text-slate-400 px-4"
                    >
                        <span class="material-symbols-outlined text-3xl mb-2">inbox</span>
                        <p class="text-xs text-center">Sin tickets que coincidan.</p>
                    </div>
                </nav>
            </aside>

            <!-- ── Columna central: chat ────────────────────────────────────── -->
            <main class="flex-1 min-w-0 flex flex-col overflow-hidden bg-slate-50 dark:bg-slate-950">

                <!-- Empty state -->
                <div v-if="!currentTicket && !loadingTicket"
                    class="flex-1 flex flex-col items-center justify-center text-slate-400 gap-4"
                >
                    <span class="material-symbols-outlined text-5xl">forum</span>
                    <div class="text-center">
                        <p class="text-sm font-semibold">Selecciona un ticket</p>
                        <p class="text-xs mt-1">Haz clic en un ticket para ver la conversación.</p>
                    </div>
                </div>

                <!-- Loading -->
                <div v-else-if="loadingTicket"
                    class="flex-1 flex items-center justify-center text-slate-400"
                >
                    <span class="material-symbols-outlined text-4xl animate-spin">progress_activity</span>
                </div>

                <!-- Chat activo — div real (no template) para que flex funcione -->
                <div v-else-if="currentTicket" class="flex-1 min-h-0 flex flex-col overflow-hidden">

                    <!-- Header fijo del chat -->
                    <header class="shrink-0 h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-5 gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-bold text-slate-800 dark:text-white truncate">
                                    {{ currentTicket.title }}
                                </h2>
                                <span class="text-xs text-slate-400 shrink-0">#{{ currentTicket.ticket_number }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span v-if="currentTicket.status"
                                    class="w-2 h-2 rounded-full shrink-0"
                                    :style="{ backgroundColor: currentTicket.status.color }"
                                />
                                <span class="text-xs text-slate-500 truncate">
                                    {{ currentTicket.status?.name ?? '—' }}
                                    <template v-if="currentTicket.assigned_user">
                                        · Asignado a <strong class="font-semibold">{{ currentTicket.assigned_user.name }}</strong>
                                    </template>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                            En vivo
                        </div>
                    </header>

                    <!-- Hilo scrollable — min-h-0 es obligatorio aquí -->
                    <div ref="threadEl" class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-5 custom-scrollbar">
                        <div v-if="sortedMessages.length === 0"
                            class="flex flex-col items-center py-16 text-slate-400 gap-3"
                        >
                            <span class="material-symbols-outlined text-4xl">chat_bubble_outline</span>
                            <p class="text-sm">Sé el primero en responder este ticket.</p>
                        </div>
                        <ChatMessage
                            v-for="msg in sortedMessages"
                            :key="msg.id"
                            :message="msg"
                            :auth-user-id="authUser?.id"
                            :can-delete="canDeleteAttachments"
                            @delete-attachment="handleDeleteMessageAttachment"
                        />
                    </div>

                    <!-- Compose fijo al fondo -->
                    <ChatCompose
                        :can-internal="can.internal"
                        :sending="sending"
                        @send="handleSend"
                    />
                </div>
            </main>

            <!-- ── Columna derecha: detalles + evidencias ───────────────────── -->
            <TicketInfoPanel
                v-if="currentTicket"
                :ticket="currentTicket"
                :can-delete="canDeleteAttachments"
                :uploading="uploadingFile"
                @upload-attachment="handleUploadAttachment"
                @delete-attachment="handleDeleteAttachment"
            />
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-slate-200 dark:bg-slate-700 rounded-full; }
</style>
