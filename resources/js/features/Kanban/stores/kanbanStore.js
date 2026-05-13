// resources/js/stores/kanbanStore.js
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
        company_id:  null, // solo super_admin
    });

    // ── Getters ──────────────────────────────────────────────────────────────

    /**
     * Tickets agrupados por status_id.
     * Usamos String() en la clave para evitar el mismatch numérico/string
     * al acceder con status.id desde Vue.
     */
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

    // ── Actions ──────────────────────────────────────────────────────────────

    async function fetchTickets(extraFilters = {}) {
        loading.value = true;
        try {
            const params = { ...filters.value, ...extraFilters };
            Object.keys(params).forEach(k => params[k] == null && delete params[k]);

            const { data } = await axios.get('/api/kanban/tickets', { params });

            // El endpoint devuelve array plano — no hay wrapper {data:[]}
            tickets.value = Array.isArray(data) ? data : (data.data ?? []);
        } catch (err) {
            console.error('[kanbanStore] fetchTickets error:', err);
            tickets.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function createTicket(payload) {
        const { data } = await axios.post('/api/kanban/tickets', payload);
        // storeTicket devuelve TicketResource → puede tener wrapper
        const ticket = data.data ?? data;
        await fetchTickets(); // Recarga completa para tener relaciones frescas
        return ticket;
    }

    async function updateTicket(id, payload) {
        const { data } = await axios.put(`/api/kanban/tickets/${id}`, payload);
        const updated = data.data ?? data;
        _replaceTicket(updated);
        return updated;
    }

    /**
     * Drag & drop: mueve ticket a nuevo estado.
     * Optimistic update: cambia status_id localmente antes de la petición.
     * Si el servidor falla, revierte.
     */
    async function moveTicket(ticketId, newStatusId) {
        const ticket      = tickets.value.find(t => t.id === ticketId);
        const prevStatusId = ticket?.status_id;

        // Optimistic: actualizar inmediatamente la vista
        if (ticket) ticket.status_id = newStatusId;

        try {
            await axios.patch(`/api/kanban/tickets/${ticketId}/move`, {
                status_id: newStatusId,
            });
        } catch (err) {
            // Revertir si el servidor rechaza
            if (ticket) ticket.status_id = prevStatusId;
            console.error('[kanbanStore] moveTicket error:', err);
            throw err;
        }
    }

    async function deleteTicket(id) {
        await axios.delete(`/api/kanban/tickets/${id}`);
        tickets.value = tickets.value.filter(t => t.id !== id);
    }

    async function addMessage(ticketId, message, isInternal) {
        const { data } = await axios.post(`/api/kanban/tickets/${ticketId}/messages`, {
            message,
            is_internal: isInternal,
        });
        const newMessage = data.data ?? data;
        const ticket = tickets.value.find(t => t.id === ticketId);
        if (ticket) {
            if (!ticket.messages) ticket.messages = [];
            ticket.messages.push(newMessage);
            ticket.messages_count = (ticket.messages_count ?? 0) + 1;
        }
        return newMessage;
    }

    async function refreshTicket(id) {
        try {
            const { data } = await axios.get(`/api/kanban/tickets/${id}`);
            // El endpoint devuelve objeto plano (sin wrapper {data:{}})
            const ticket = Array.isArray(data) ? data[0] : (data.data ?? data);
            _replaceTicket(ticket);
            return ticket;
        } catch (err) {
            console.error(`[kanbanStore] refreshTicket(${id}) error:`, err.response?.data ?? err.message);
            throw err; // Re-lanzar para que el modal pueda capturarlo
        }
    }

    function setFilter(key, value) {
        filters.value[key] = (value === '' || value === 0) ? null : value;
    }

    async function applyFilter(key, value) {
        setFilter(key, value);
        await fetchTickets();
    }

    async function clearFilters() {
        Object.keys(filters.value).forEach(k => { filters.value[k] = null; });
        await fetchTickets();
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    function _replaceTicket(updated) {
        const idx = tickets.value.findIndex(t => t.id === updated.id);
        if (idx !== -1) {
            tickets.value.splice(idx, 1, updated);
        }
    }

    return {
        tickets, loading, filters,
        ticketsByStatus, urgentCount, openCount, activeFilterCount,
        fetchTickets, applyFilter, clearFilters, createTicket, updateTicket, moveTicket,
        deleteTicket, addMessage, refreshTicket,
        setFilter, clearFilters,
    };
});
