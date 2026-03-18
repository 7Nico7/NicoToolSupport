// resources/js/features/Kanban/composables/useTicketChannel.js
//
// ─────────────────────────────────────────────────────────────────────────────
// Composable que gestiona la suscripción al canal privado de Echo de un ticket.
//
// RESPONSABILIDADES:
//   - Suscribirse a `private-ticket.{ticketId}` cuando se abre un ticket
//   - Escuchar el evento `.message.sent` (broadcastAs en MessageSent.php)
//   - Llamar onMessage(data) cuando llega un mensaje nuevo en tiempo real
//   - Desuscribirse automáticamente en onUnmounted o cuando ticketId cambia
//
// USO en EditTicketModal.vue:
//
//   useTicketChannel(
//       () => props.ticketId,
//       (msg) => {
//           // msg = { id, ticket_id, message, is_internal, created_at, user }
//           // Solo añadir si no existe ya (deduplicación vs HTTP response)
//           if (ticket.value && !ticket.value.messages?.some(m => m.id === msg.id)) {
//               ticket.value.messages = [...(ticket.value.messages ?? []), msg]
//           }
//       }
//   )
//
// PRE-REQUISITO: window.Echo debe existir (configurado por resources/js/echo.js)
// ─────────────────────────────────────────────────────────────────────────────

import { watch, onUnmounted } from 'vue'

/**
 * @param {() => number|null}  getTicketId  Getter reactivo del ID del ticket
 * @param {(msg: object)=>void} onMessage    Callback llamado con cada mensaje nuevo
 */
export function useTicketChannel(getTicketId, onMessage) {
    let channel = null

    const channelName = (id) => `ticket.${id}`

    const subscribe = (ticketId) => {
        if (!ticketId) return
        if (!window.Echo) {
            console.warn('[useTicketChannel] window.Echo no está disponible. '
                + 'Asegúrate de haber ejecutado php artisan install:broadcasting '
                + 'e importado resources/js/echo.js en app.js')
            return
        }

        // Limpiar suscripción anterior si existía
        unsubscribe()

        channel = window.Echo
            .private(channelName(ticketId))
            // El punto inicial indica evento con broadcastAs() personalizado
            // (no nombre de clase PHP). Corresponde a broadcastAs() = 'message.sent'
            .listen('.message.sent', (data) => {
                onMessage(data)
            })
    }

    const unsubscribe = () => {
        if (!channel) return
        // Dejar el canal limpiamente — libera el WebSocket si nadie más lo usa
        window.Echo?.leave(channelName(getTicketId() ?? ''))
        channel = null
    }

    // Reacciona a cambios de ticketId: nuevo ticket → nueva suscripción
    // null/undefined → desuscribirse (modal cerrado)
    watch(
        getTicketId,
        (id) => {
            if (id) subscribe(id)
            else unsubscribe()
        },
        { immediate: true }
    )

    // Limpieza garantizada cuando el componente se destruye
    onUnmounted(unsubscribe)
}
