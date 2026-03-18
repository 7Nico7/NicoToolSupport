// resources/js/features/Gantt/stores/ganttStore.js  ← path corregido (antes decía resources/js/stores/)
//
// Arquitectura nueva (Inertia props):
//   Los tickets llegan como prop de Inertia — el store NO hace fetch axios.
//   El store solo gestiona:
//     - tickets en memoria (recibidos del prop)
//     - UI state: groupBy, colorBy, zoom, loading
//     - Computed de geometría para el diagrama
//
// Flujo:
//   GanttController → Inertia prop → Index.vue → store.setTickets()
//   Filtros → router.get() con due_after/due_before + filtros extra → nuevo Inertia render

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useGanttStore = defineStore('gantt', () => {

    // ── State ────────────────────────────────────────────────────────────────
    const tickets   = ref([]);
    const groupBy   = ref('project'); // 'project' | 'agent' | 'status' | 'company'
    const colorBy   = ref('status');  // 'status'  | 'priority'
    const zoom      = ref('month');   // 'week'    | 'month'
    const loading   = ref(false);     //  añadido: GanttChart.vue lo consume en v-else-if

    // Catálogo id→name de compañías — solo super_admin
    const companies = ref([]);

    // ── Getters ──────────────────────────────────────────────────────────────

    const companyMap = computed(() =>
        Object.fromEntries((companies.value ?? []).map(c => [c.id, c.name]))
    );

    const dateRange = computed(() => {
        if (tickets.value.length === 0) return null;

        const starts = tickets.value.map(t => new Date(t.start_date).getTime());
        const ends   = tickets.value.map(t => new Date(t.due_date).getTime());

        const min = new Date(Math.min(...starts));
        const max = new Date(Math.max(...ends));

        min.setDate(min.getDate() - 3);
        max.setDate(max.getDate() + 3);

        return { start: min, end: max, totalDays: daysBetween(min, max) };
    });

    const groupedTickets = computed(() => {
        const groups = {};

        tickets.value.forEach(ticket => {
            let key, label, color;

            if (groupBy.value === 'company') {
                key   = ticket.company_id ?? 'none';
                label = companyMap.value[ticket.company_id] ?? 'Sin compañía';
                color = null;
            } else if (groupBy.value === 'project') {
                key   = ticket.project_id ?? 'none';
                label = ticket.project?.name ?? 'Sin proyecto';
                color = null;
            } else if (groupBy.value === 'agent') {
                key   = ticket.assigned_to ?? 'none';
                label = ticket.assigned_user?.name ?? 'Sin asignar';
                color = null;
            } else {
                key   = ticket.status_id ?? 'none';
                label = ticket.status?.name ?? 'Sin estado';
                color = ticket.status?.color ?? '#94a3b8';
            }

            if (!groups[key]) {
                groups[key] = { key: String(key), label, color, tickets: [] };
            }
            groups[key].tickets.push(ticket);
        });

        return Object.values(groups).sort((a, b) => a.label.localeCompare(b.label));
    });

    const totalTickets = computed(() => tickets.value.length);

    const overdueCount = computed(() => {
        const now = new Date();
        return tickets.value.filter(t =>
            new Date(t.due_date) < now && !t.closed_at
        ).length;
    });

    const todayPosition = computed(() => {
        if (!dateRange.value) return null;
        const { start, totalDays } = dateRange.value;
        const today = new Date();
        if (today < start || today > dateRange.value.end) return null;
        return (daysBetween(start, today) / totalDays) * 100;
    });

    // ── Actions ──────────────────────────────────────────────────────────────

    function setTickets(list)   { tickets.value   = Array.isArray(list) ? list : []; }
    function setCompanies(list) { companies.value  = Array.isArray(list) ? list : []; }
    function setLoading(value)  { loading.value    = !!value; }

    function setGroupBy(value)  { groupBy.value = value; }
    function setColorBy(value)  { colorBy.value = value; }
    function setZoom(value)     { zoom.value    = value; }

    // ── Helpers de geometría ─────────────────────────────────────────────────

    function barGeometry(ticket) {
        if (!dateRange.value) return { left: 0, width: 0 };
        const { start, totalDays } = dateRange.value;

        const ticketStart  = new Date(ticket.start_date);
        const ticketEnd    = new Date(ticket.due_date);
        const offsetDays   = daysBetween(start, ticketStart);
        const durationDays = Math.max(1, daysBetween(ticketStart, ticketEnd));

        return {
            left:  Math.max(0, (offsetDays / totalDays) * 100),
            width: Math.min(100, (durationDays / totalDays) * 100),
        };
    }

    function barColor(ticket) {
        if (colorBy.value === 'priority') return ticket.priority?.color ?? '#94a3b8';
        return ticket.status?.color ?? '#3b82f6';
    }

    function timelineColumns() {
        if (!dateRange.value) return [];
        const { start, end, totalDays } = dateRange.value;

        const columns = [];
        const cursor  = new Date(start);

        if (zoom.value === 'week') {
            while (cursor <= end) {
                const weekStart = new Date(cursor);
                cursor.setDate(cursor.getDate() + 7);
                const days = Math.min(7, daysBetween(weekStart, end) + 1);
                columns.push({
                    label:        `Sem ${getWeekNumber(weekStart)}`,
                    subLabel:     weekStart.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' }),
                    widthPercent: (days / totalDays) * 100,
                    date:         new Date(weekStart),
                });
            }
        } else {
            while (cursor <= end) {
                const monthStart = new Date(cursor.getFullYear(), cursor.getMonth(), 1);
                const monthEnd   = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0);
                const days = Math.min(
                    daysBetween(monthStart, monthEnd) + 1,
                    daysBetween(cursor > monthStart ? cursor : monthStart, end) + 1
                );
                columns.push({
                    label:        cursor.toLocaleDateString('es-MX', { month: 'long', year: 'numeric' }),
                    subLabel:     null,
                    widthPercent: (days / totalDays) * 100,
                    date:         new Date(monthStart),
                });
                cursor.setMonth(cursor.getMonth() + 1);
                cursor.setDate(1);
            }
        }
        return columns;
    }

    // ── Return ───────────────────────────────────────────────────────────────

    return {
        // State
        tickets, groupBy, colorBy, zoom, loading, companies,
        // Getters
        companyMap, dateRange, groupedTickets,
        totalTickets, overdueCount, todayPosition,
        // Actions
        setTickets, setCompanies, setLoading,
        setGroupBy, setColorBy, setZoom,
        // Helpers
        barGeometry, barColor, timelineColumns,
    };
});

// ── Utils privados ────────────────────────────────────────────────────────────

function daysBetween(a, b) {
    const msPerDay = 1000 * 60 * 60 * 24;
    return Math.max(0, Math.round((b - a) / msPerDay));
}

function getWeekNumber(date) {
    const d      = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
}
