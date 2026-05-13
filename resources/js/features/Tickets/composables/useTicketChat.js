// resources/js/features/tickets/composables/useTicketChat.js
//
// Composable que encapsula TODA la lógica de negocio de la vista de chat.
// Show.vue solo declara props y renderiza — este composable hace el trabajo.
//
// Responsabilidades:
//   - Cargar ticket activo al hacer click en el sidebar
//   - Mantener el ticket local con sus mensajes y evidencias
//   - Enviar mensajes (con y sin archivos)
//   - Subir / eliminar evidencias del ticket
//   - Eliminar adjuntos de mensajes
//   - Auto-scroll del hilo
//   - Suscripción Echo en tiempo real (via useTicketChannel)
//   - Búsqueda local en la lista de tickets

import { ref, computed, watch, nextTick } from 'vue'
import axios from 'axios'
import { useAuth } from '@/shared/composables/useAuth'
import { useTicketChannel } from '@/features/Kanban/composables/useTicketChannel'

export function useTicketChat(props) {
    const { authUser } = useAuth()

    // ── Estado ────────────────────────────────────────────────────────────────
    const currentTicket = ref(props.activeTicket ?? null)
    const loadingTicket = ref(false)
    const sending       = ref(false)
    const uploadingFile = ref(false)
    const search        = ref('')
    const threadEl      = ref(null)  // ref del div scrollable del hilo

    // ── Lista filtrada ────────────────────────────────────────────────────────
    const filteredTickets = computed(() => {
        const q = search.value.toLowerCase().trim()
        if (!q) return props.tickets
        return props.tickets.filter(t =>
            t.title.toLowerCase().includes(q) ||
            t.ticket_number.toLowerCase().includes(q)
        )
    })

    // ── Mensajes ordenados ────────────────────────────────────────────────────
    const sortedMessages = computed(() =>
        [...(currentTicket.value?.messages ?? [])].sort(
            (a, b) => new Date(a.created_at) - new Date(b.created_at)
        )
    )

    // ── Permisos ──────────────────────────────────────────────────────────────
    const canDeleteAttachments = computed(() =>
        props.can?.delete || ['admin', 'super_admin'].includes(authUser.value?.role)
    )

    // ── Auto-scroll ───────────────────────────────────────────────────────────
    const isNearBottom = () => {
        if (!threadEl.value) return true
        const { scrollTop, scrollHeight, clientHeight } = threadEl.value
        return scrollHeight - scrollTop - clientHeight <= 100
    }

    const scrollToBottom = () => {
        nextTick(() => {
            if (threadEl.value) threadEl.value.scrollTop = threadEl.value.scrollHeight
        })
    }

    // Scroll al fondo cada vez que cambia el ticket activo
    watch(currentTicket, (val) => {
        if (val) nextTick(scrollToBottom)
    })

    // ── Canal Echo ────────────────────────────────────────────────────────────
    useTicketChannel(
        () => currentTicket.value?.id ?? null,
        (msg) => {
            if (!currentTicket.value) return
            const exists = currentTicket.value.messages?.some(m => m.id === msg.id)
            if (!exists) {
                currentTicket.value = {
                    ...currentTicket.value,
                    messages: [...(currentTicket.value.messages ?? []), { ...msg, attachments: [] }],
                }
                if (isNearBottom()) scrollToBottom()
            }
        }
    )

    // ── Seleccionar ticket del sidebar ────────────────────────────────────────
    const selectTicket = async (ticketId) => {
        if (currentTicket.value?.id === ticketId) return
        loadingTicket.value = true
        try {
            const { data } = await axios.get(route('tickets.chat.show', ticketId))
            currentTicket.value = data
        } catch (err) {
            console.error('[useTicketChat] selectTicket error:', err)
        } finally {
            loadingTicket.value = false
        }
    }

    // ── Enviar mensaje (+ archivos adjuntos opcionales) ───────────────────────
    const handleSend = async ({ message, is_internal, files, onDone, onError }) => {
        if (!currentTicket.value) return
        sending.value = true
        try {
            let payload, config = {}

            if (files?.length > 0) {
                payload = new FormData()
                payload.append('message',     message)
                payload.append('is_internal', is_internal ? '1' : '0')
                files.forEach(f => payload.append('files[]', f))
                config = { headers: { 'Content-Type': 'multipart/form-data' } }
            } else {
                payload = { message, is_internal }
            }

            const { data } = await axios.post(
                `/api/kanban/tickets/${currentTicket.value.id}/messages`,
                payload,
                config
            )

            const newMsg = data.data ?? data
            const exists = currentTicket.value.messages?.some(m => m.id === newMsg.id)
            if (!exists) {
                currentTicket.value = {
                    ...currentTicket.value,
                    messages: [...(currentTicket.value.messages ?? []), newMsg],
                }
                scrollToBottom()
            }
            onDone()
        } catch (err) {
            console.error('[useTicketChat] handleSend error:', err)
            onError()
        } finally {
            sending.value = false
        }
    }

    // ── Evidencias del ticket ─────────────────────────────────────────────────
    const handleUploadAttachment = async ({ file, description, onDone, onError }) => {
        if (!currentTicket.value) return
        uploadingFile.value = true
        try {
            const formData = new FormData()
            formData.append('file',        file)
            formData.append('description', description)

            const { data } = await axios.post(
                `/api/kanban/tickets/${currentTicket.value.id}/attachments`,
                formData,
                { headers: { 'Content-Type': 'multipart/form-data' } }
            )

            currentTicket.value = {
                ...currentTicket.value,
                attachments: [...(currentTicket.value.attachments ?? []), data.data ?? data],
            }
            onDone()
        } catch (err) {
            console.error('[useTicketChat] handleUploadAttachment error:', err)
            onError()
        } finally {
            uploadingFile.value = false
        }
    }

    const handleDeleteAttachment = async (attachmentId) => {
        if (!currentTicket.value) return
        try {
            await axios.delete(`/api/kanban/ticket-attachments/${attachmentId}`)
            currentTicket.value = {
                ...currentTicket.value,
                attachments: currentTicket.value.attachments.filter(a => a.id !== attachmentId),
            }
        } catch (err) {
            console.error('[useTicketChat] handleDeleteAttachment error:', err)
        }
    }

    // ── Adjuntos de mensajes ──────────────────────────────────────────────────
    const handleDeleteMessageAttachment = async (attachmentId) => {
        if (!currentTicket.value) return
        try {
            await axios.delete(`/api/kanban/message-attachments/${attachmentId}`)
            currentTicket.value = {
                ...currentTicket.value,
                messages: currentTicket.value.messages.map(m => ({
                    ...m,
                    attachments: (m.attachments ?? []).filter(a => a.id !== attachmentId),
                })),
            }
        } catch (err) {
            console.error('[useTicketChat] handleDeleteMessageAttachment error:', err)
        }
    }

    return {
        // Estado
        currentTicket,
        loadingTicket,
        sending,
        uploadingFile,
        search,
        threadEl,

        // Computed
        filteredTickets,
        sortedMessages,
        canDeleteAttachments,
        authUser,

        // Acciones
        selectTicket,
        handleSend,
        handleUploadAttachment,
        handleDeleteAttachment,
        handleDeleteMessageAttachment,
    }
}
