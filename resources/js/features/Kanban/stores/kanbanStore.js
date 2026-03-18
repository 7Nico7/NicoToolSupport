// resources/js/features/Kanban/stores/kanbanStore.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useKanbanStore = defineStore('kanban', () => {
    // ── State ────────────────────────────────────────────────────────────────
    const tickets = ref([]);
    const loading = ref(false);
    const filters = ref({
        status_id:   null,
        priority_id: null,
        assigned_to: null,
        project_id:  null,
        category_id: null,
        type_id:     null,
        search:      null,
    });

    // ── Getters ──────────────────────────────────────────────────────────────

    const ticketsByStatus = computed(() => {
        const map = {};
        tickets.value.forEach(t => {
            const key = String(t.status_id);
            if (!map[key]) map[key] = [];
            map[key].push(t);
        });
        return map;
    });

    const urgentCount = computed(() =>
        tickets.value.filter(t => t.priority?.level >= 3).length
    );

    const openCount = computed(() => tickets.value.length);

    const activeFilterCount = computed(() =>
        Object.values(filters.value).filter(v => v !== null && v !== '').length
    );

    // ── Actions — Tickets ─────────────────────────────────────────────────────

    async function fetchTickets(extraFilters = {}) {
        loading.value = true;
        try {
            const params = { ...filters.value, ...extraFilters };
            Object.keys(params).forEach(k => {
                if (params[k] === null || params[k] === '') delete params[k];
            });
            const { data } = await axios.get('/api/kanban/tickets', { params });
            tickets.value = Array.isArray(data) ? data : (data.data ?? []);
        } catch (err) {
            console.error('[kanbanStore] fetchTickets error:', err);
            tickets.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function applyFilter(key, value) {
        setFilter(key, value);
        await fetchTickets();
    }

    async function clearFilters() {
        Object.keys(filters.value).forEach(k => { filters.value[k] = null; });
        await fetchTickets();
    }

    async function createTicket(payload) {
        const { data } = await axios.post('/api/kanban/tickets', payload);
        await fetchTickets();
        return data.data ?? data;
    }

    async function updateTicket(id, payload) {
        const { data } = await axios.put(`/api/kanban/tickets/${id}`, payload);
        const updated = data.data ?? data;
        _replaceTicket(updated);
        return updated;
    }

    async function moveTicket(ticketId, newStatusId) {
        const ticket       = tickets.value.find(t => t.id === ticketId);
        const prevStatusId = ticket?.status_id;

        if (ticket) ticket.status_id = newStatusId;

        try {
            await axios.patch(`/api/kanban/tickets/${ticketId}/move`, { status_id: newStatusId });
        } catch (err) {
            if (ticket) ticket.status_id = prevStatusId;
            console.error('[kanbanStore] moveTicket error:', err);
            throw err;
        }
    }

    async function deleteTicket(id) {
        await axios.delete(`/api/kanban/tickets/${id}`);
        tickets.value = tickets.value.filter(t => t.id !== id);
    }

    async function refreshTicket(id) {
        try {
            const { data } = await axios.get(`/api/kanban/tickets/${id}`);
            const ticket = Array.isArray(data) ? data[0] : (data.data ?? data);
            _replaceTicket(ticket);
            return ticket;
        } catch (err) {
            console.error(`[kanbanStore] refreshTicket(${id}) error:`, err.response?.data ?? err.message);
            throw err;
        }
    }

    // ── Actions — Mensajes ────────────────────────────────────────────────────

    /**
     * Envía un mensaje, opcionalmente con archivos adjuntos.
     *
     * Si `files` es un array con al menos un File, usa FormData para el multipart.
     * Si no hay archivos, usa JSON como antes — sin cambio de comportamiento.
     *
     * El backend debe devolver el mensaje con sus attachments ya serializados:
     *   { id, message, is_internal, created_at, user, attachments: [...] }
     */
    async function addMessage(ticketId, message, isInternal, files = []) {
        let payload;
        let config = {};

        if (files.length > 0) {
            // Multipart: texto + archivos en una sola petición
            payload = new FormData();
            payload.append('message',     message);
            payload.append('is_internal', isInternal ? '1' : '0');
            files.forEach(f => payload.append('files[]', f));
            config = { headers: { 'Content-Type': 'multipart/form-data' } };
        } else {
            payload = { message, is_internal: isInternal };
        }

        const { data } = await axios.post(
            `/api/kanban/tickets/${ticketId}/messages`,
            payload,
            config
        );

        const newMessage = data.data ?? data;

        // Actualizar el ticket en el store (KanbanColumn ve messages_count)
        const ticket = tickets.value.find(t => t.id === ticketId);
        if (ticket) {
            if (!ticket.messages) ticket.messages = [];
            ticket.messages.push(newMessage);
            ticket.messages_count = (ticket.messages_count ?? 0) + 1;
        }

        return newMessage;
    }

    async function deleteMessageAttachment(messageId, attachmentId) {
        await axios.delete(`/api/kanban/message-attachments/${attachmentId}`);
        // Actualizar el mensaje en todos los tickets del store
        tickets.value.forEach(ticket => {
            if (!ticket.messages) return;
            const msg = ticket.messages.find(m => m.id === messageId);
            if (msg?.attachments) {
                msg.attachments = msg.attachments.filter(a => a.id !== attachmentId);
            }
        });
    }

    // ── Actions — Evidencias del ticket ──────────────────────────────────────

    /**
     * Sube una evidencia directa al ticket (tab Archivos).
     * `description` es el contexto que explica qué muestra el archivo.
     * Retorna el attachment serializado con download_url.
     */
    async function uploadTicketAttachment(ticketId, file, description = '') {
        const formData = new FormData();
        formData.append('file',        file);
        formData.append('description', description);

        const { data } = await axios.post(
            `/api/kanban/tickets/${ticketId}/attachments`,
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        const attachment = data.data ?? data;

        // Añadir al array de attachments del ticket en el store
        const ticket = tickets.value.find(t => t.id === ticketId);
        if (ticket) {
            if (!ticket.attachments) ticket.attachments = [];
            ticket.attachments.push(attachment);
        }

        return attachment;
    }

    async function deleteTicketAttachment(ticketId, attachmentId) {
        await axios.delete(`/api/kanban/ticket-attachments/${attachmentId}`);
        const ticket = tickets.value.find(t => t.id === ticketId);
        if (ticket?.attachments) {
            ticket.attachments = ticket.attachments.filter(a => a.id !== attachmentId);
        }
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    function setFilter(key, value) {
        filters.value[key] = (value === '' || value === 0) ? null : value;
    }

    function _replaceTicket(updated) {
        const idx = tickets.value.findIndex(t => t.id === updated.id);
        if (idx !== -1) tickets.value.splice(idx, 1, updated);
    }

    return {
        // State
        tickets, loading, filters,
        // Getters
        ticketsByStatus, urgentCount, openCount, activeFilterCount,
        // Ticket actions
        fetchTickets, applyFilter, clearFilters,
        createTicket, updateTicket, moveTicket, deleteTicket, refreshTicket,
        setFilter,
        // Message actions
        addMessage, deleteMessageAttachment,
        // Ticket attachment actions
        uploadTicketAttachment, deleteTicketAttachment,
    };
});
