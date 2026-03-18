<script setup>
// resources/js/features/Gantt/components/GanttChart.vue
// Diagrama de Gantt construido en Vue puro con Tailwind.
// Arquitectura interna:
//   - Panel izquierdo FIJO: grupos + etiquetas de tickets
//   - Panel derecho SCROLLABLE: timeline header + barras
//   - Ambos paneles sincronizan el scroll vertical con JS
// El store (ganttStore) provee toda la lógica de fechas/geometría.

import { ref, computed, onMounted, nextTick } from 'vue';
import { useGanttStore } from '@/features/Gantt/stores/ganttStore';
import { useAuth } from '@/shared/composables/useAuth';

const props = defineProps({
    onEditTicket:          { type: Function, default: null  },
    requiresCompanyFilter: { type: Boolean,  default: false },
    // isSuperAdmin eliminado como prop — se obtiene de useAuth()
});

const store = useGanttStore();
const { isSuperAdmin } = useAuth(); // fuente única para el rol

// ── Refs DOM ─────────────────────────────────────────────────────────────────
const leftPanel  = ref(null);
const rightPanel = ref(null);
const isSyncing  = ref(false);

// ── Tooltip ──────────────────────────────────────────────────────────────────
const tooltip = ref({ visible: false, ticket: null, x: 0, y: 0 });

const showTooltip = (ticket, event) => {
    tooltip.value = { visible: true, ticket, x: event.clientX + 12, y: event.clientY - 40 };
};
const hideTooltip = () => { tooltip.value.visible = false; };

// ── Sincronizar scroll vertical entre los dos paneles ─────────────────────────
const syncScroll = (source, target) => {
    if (isSyncing.value) return;
    isSyncing.value = true;
    target.scrollTop = source.scrollTop;
    nextTick(() => { isSyncing.value = false; });
};

onMounted(() => {
    const l = leftPanel.value;
    const r = rightPanel.value;
    if (!l || !r) return;
    l.addEventListener('scroll', () => syncScroll(l, r));
    r.addEventListener('scroll', () => syncScroll(r, l));
});

// ── Columnas del timeline ─────────────────────────────────────────────────────
const COL_MIN_PX = computed(() => store.zoom === 'week' ? 90 : 140);
const columns = computed(() => store.timelineColumns());
const timelineMinWidthPx = computed(() => columns.value.length * COL_MIN_PX.value);

// ── Indicador "Hoy" ──────────────────────────────────────────────────────────
const todayPct = computed(() => store.todayPosition);

// ── Helpers de formato ───────────────────────────────────────────────────────
const fmtDate = (d) => {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });
};

const isOverdue = (ticket) => !ticket.closed_at && new Date(ticket.due_date) < new Date();

const ROW_HEIGHT = 40; // px — debe coincidir con h-10 de cada fila
</script>

<template>
    <div class="flex flex-col h-full select-none">

        <!-- ── Controles del Gantt ─────────────────────────────────────────── -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">

            <!-- Agrupación -->
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Agrupar por</span>
                <div class="flex rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-800">
                    <button
                        v-for="opt in [
                            { value: 'project', label: 'Proyecto', icon: 'folder' },
                            { value: 'agent',   label: 'Agente',   icon: 'person' },
                            { value: 'status',  label: 'Estado',   icon: 'circle' },
                        ]"
                        :key="opt.value"
                        type="button"
                        @click="store.setGroupBy(opt.value)"
                        :class="[
                            'flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold transition-all',
                            store.groupBy === opt.value
                                ? 'bg-brand text-white'
                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700',
                        ]"
                    >
                        <span class="material-symbols-outlined text-xs">{{ opt.icon }}</span>
                        {{ opt.label }}
                    </button>
                </div>
            </div>

            <!-- Colorear por -->
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Color</span>
                <div class="flex rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-800">
                    <button
                        v-for="opt in [{ value: 'status', label: 'Estado' }, { value: 'priority', label: 'Prioridad' }]"
                        :key="opt.value"
                        type="button"
                        @click="store.setColorBy(opt.value)"
                        :class="[
                            'px-3 py-1.5 text-xs font-semibold transition-all',
                            store.colorBy === opt.value
                                ? 'bg-brand text-white'
                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700',
                        ]"
                    >{{ opt.label }}</button>
                </div>
            </div>
        </div>

        <!-- ── Empty state: super_admin debe elegir compañía ───────────────── -->
        <div
            v-if="requiresCompanyFilter"
            class="flex-1 flex flex-col items-center justify-center gap-5 py-20"
        >
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-brand/10">
                <span class="material-symbols-outlined text-4xl text-brand">corporate_fare</span>
            </div>
            <div class="text-center max-w-xs">
                <p class="text-base font-black text-slate-800 dark:text-white">Selecciona una compañía</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Abre el panel de <strong class="text-slate-600 dark:text-slate-300">Filtros</strong>
                    y elige una compañía para cargar su diagrama de Gantt.
                </p>
            </div>
            <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-sm">filter_alt</span>
                Filtros → Compañía
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                <span class="material-symbols-outlined text-sm">business</span>
                Seleccionar empresa
            </div>
        </div>

        <!-- ── Empty state: sin tickets ─────────────────────────────────────── -->
        <!-- store.loading ahora existe en el store (fue añadido) -->
        <div
            v-else-if="!store.loading && store.totalTickets === 0"
            class="flex-1 flex flex-col items-center justify-center text-slate-400 gap-4"
        >
            <span class="material-symbols-outlined text-5xl">date_range</span>
            <div class="text-center">
                <p class="text-sm font-semibold">Sin tickets con fecha de vencimiento</p>
                <p class="text-xs mt-1">Solo se muestran tickets que tienen fecha de vencimiento definida.</p>
            </div>
        </div>

        <!-- ── Gantt board ─────────────────────────────────────────────────── -->
        <div v-else class="flex flex-1 min-h-0 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-800">

            <!-- Panel izquierdo — etiquetas (fijo) -->
            <div
                ref="leftPanel"
                class="w-64 shrink-0 border-r border-slate-200 dark:border-slate-700 overflow-y-auto overflow-x-hidden"
                style="scrollbar-width: none;"
            >
                <div class="h-12 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 flex items-center px-4">
                    <span class="text-xs font-black text-slate-500 uppercase tracking-wider">Ticket</span>
                </div>

                <template v-for="group in store.groupedTickets" :key="group.key">
                    <div class="flex items-center gap-2 px-4 py-2 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-10">
                        <div v-if="group.color" class="h-2.5 w-2.5 rounded-full shrink-0" :style="{ backgroundColor: group.color }" />
                        <span class="text-xs font-black text-slate-700 dark:text-slate-200 truncate">{{ group.label }}</span>
                        <span class="ml-auto text-[10px] font-bold text-slate-400 shrink-0">{{ group.tickets.length }}</span>
                    </div>

                    <div
                        v-for="ticket in group.tickets"
                        :key="ticket.id"
                        class="flex items-center gap-2 px-4 border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 cursor-pointer transition-colors"
                        style="height: 40px;"
                        @click="onEditTicket && onEditTicket(ticket.id)"
                    >
                        <span class="text-[10px] font-black text-slate-400 shrink-0">{{ ticket.ticket_number }}</span>
                        <span class="text-xs text-slate-700 dark:text-slate-300 truncate flex-1">{{ ticket.title }}</span>
                        <span v-if="isOverdue(ticket)" class="material-symbols-outlined text-xs text-danger shrink-0" title="Vencido">warning</span>
                    </div>
                </template>
            </div>

            <!-- Panel derecho — timeline (scrollable) -->
            <div ref="rightPanel" class="flex-1 overflow-auto relative">

                <!-- Timeline header -->
                <div
                    class="sticky top-0 z-20 flex border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 h-12"
                    :style="{ minWidth: `${timelineMinWidthPx}px` }"
                >
                    <div
                        v-for="col in columns"
                        :key="col.label"
                        class="border-r border-slate-200 dark:border-slate-700 px-2 flex flex-col justify-center overflow-hidden"
                        :style="{ flex: 1, minWidth: `${COL_MIN_PX}px` }"
                    >
                        <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 capitalize truncate">{{ col.label }}</span>
                        <span v-if="col.subLabel" class="text-[9px] text-slate-400 truncate">{{ col.subLabel }}</span>
                    </div>
                </div>

                <!-- Barras del Gantt -->
                <div class="relative" :style="{ minWidth: `${timelineMinWidthPx}px` }">

                    <!-- Indicador "Hoy" -->
                    <div v-if="todayPct !== null" class="absolute top-0 bottom-0 z-30 pointer-events-none" :style="{ left: `${todayPct}%` }">
                        <div class="relative h-full">
                            <div class="absolute top-0 bottom-0 w-px bg-brand/50" />
                            <div class="absolute -top-0 left-0.5 text-[9px] font-black text-brand bg-brand/10 px-1 rounded">HOY</div>
                        </div>
                    </div>

                    <!-- Columnas de fondo (zebra) -->
                    <div class="absolute inset-0 flex pointer-events-none">
                        <div
                            v-for="(col, i) in columns"
                            :key="i"
                            :class="['border-r border-slate-100 dark:border-slate-700/40 h-full', i % 2 === 1 ? 'bg-slate-50/50 dark:bg-slate-800/20' : '']"
                            :style="{ flex: 1, minWidth: `${COL_MIN_PX}px` }"
                        />
                    </div>

                    <!-- Grupos + filas de barras -->
                    <template v-for="group in store.groupedTickets" :key="group.key">
                        <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50" style="height: 33px;" />

                        <div
                            v-for="ticket in group.tickets"
                            :key="ticket.id"
                            class="relative border-b border-slate-100 dark:border-slate-700/50"
                            style="height: 40px;"
                        >
                            <div
                                class="absolute top-1/2 -translate-y-1/2 rounded-full h-5 flex items-center px-2 cursor-pointer group/bar transition-all hover:brightness-110 hover:scale-y-110"
                                :style="{
                                    left:            `${store.barGeometry(ticket).left}%`,
                                    width:           `${Math.max(store.barGeometry(ticket).width, 1.5)}%`,
                                    backgroundColor: store.barColor(ticket),
                                    opacity:         ticket.closed_at ? 0.5 : 1,
                                    outline:         isOverdue(ticket) ? '2px solid #ef4444' : 'none',
                                    outlineOffset:   '1px',
                                }"
                                @mouseenter="showTooltip(ticket, $event)"
                                @mouseleave="hideTooltip"
                                @click="onEditTicket && onEditTicket(ticket.id)"
                            >
                                <span class="text-[10px] font-bold text-white truncate leading-none opacity-0 group-hover/bar:opacity-100 transition-opacity">{{ ticket.ticket_number }}</span>
                                <span v-if="ticket.closed_at" class="material-symbols-outlined text-[12px] text-white/80 ml-auto shrink-0">check_circle</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ── Tooltip flotante ────────────────────────────────────────────── -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-100 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
                <div
                    v-if="tooltip.visible && tooltip.ticket"
                    class="fixed z-50 pointer-events-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl px-4 py-3 max-w-xs"
                    :style="{ left: `${tooltip.x}px`, top: `${tooltip.y}px` }"
                >
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[10px] font-black text-brand bg-brand/10 px-1.5 py-0.5 rounded">{{ tooltip.ticket.ticket_number }}</span>
                        <div class="h-2 w-2 rounded-full shrink-0" :style="{ backgroundColor: tooltip.ticket.status?.color }" />
                        <span class="text-xs text-slate-500">{{ tooltip.ticket.status?.name }}</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white leading-snug mb-2">{{ tooltip.ticket.title }}</p>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="material-symbols-outlined text-xs">play_arrow</span>
                            Inicio: {{ fmtDate(tooltip.ticket.start_date) }}
                        </div>
                        <div class="flex items-center gap-2 text-xs" :class="isOverdue(tooltip.ticket) ? 'text-danger font-semibold' : 'text-slate-500'">
                            <span class="material-symbols-outlined text-xs">{{ isOverdue(tooltip.ticket) ? 'warning' : 'schedule' }}</span>
                            Vence: {{ fmtDate(tooltip.ticket.due_date) }}
                            <span v-if="isOverdue(tooltip.ticket)">(vencido)</span>
                        </div>
                        <div v-if="tooltip.ticket.assigned_user" class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="material-symbols-outlined text-xs">person</span>
                            {{ tooltip.ticket.assigned_user.name }}
                        </div>
                        <div v-if="tooltip.ticket.priority" class="flex items-center gap-2 text-xs">
                            <div class="h-2 w-2 rounded-full shrink-0" :style="{ backgroundColor: tooltip.ticket.priority.color }" />
                            <span class="text-slate-500">{{ tooltip.ticket.priority.name }}</span>
                        </div>
                    </div>
                    <p v-if="onEditTicket" class="text-[10px] text-slate-400 mt-2 pt-2 border-t border-slate-100 dark:border-slate-700">Clic para ver detalles</p>
                </div>
            </Transition>
        </Teleport>

        <!-- ── Leyenda ─────────────────────────────────────────────────────── -->
        <div class="flex items-start gap-6 mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 flex-wrap">
            <span class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider shrink-0 pt-0.5">Leyenda</span>
            <div class="flex items-center gap-6 flex-wrap flex-1">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-8 rounded-full bg-brand" />
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">Barra normal</p>
                        <p class="text-[10px] text-slate-400">Ticket activo. Color según Estado o Prioridad.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-8 rounded-full bg-slate-400 opacity-50 flex items-center justify-end pr-1">
                        <span class="material-symbols-outlined text-[9px] text-white">check_circle</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">Cerrado</p>
                        <p class="text-[10px] text-slate-400">Ticket resuelto. Semitransparente con ✓.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-8 rounded-full bg-danger/70 outline outline-2 outline-danger outline-offset-1" />
                    <div>
                        <p class="text-xs font-semibold text-danger">Vencido</p>
                        <p class="text-[10px] text-slate-400">Fecha de vencimiento pasada y ticket abierto.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8">
                        <div class="h-4 w-0.5 bg-brand rounded" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-brand">Línea "Hoy"</p>
                        <p class="text-[10px] text-slate-400">Marca la fecha actual en el diagrama.</p>
                    </div>
                </div>
            </div>
            <p class="text-xs text-slate-400 self-center shrink-0">
                <span class="font-semibold text-slate-600 dark:text-slate-300">{{ store.totalTickets }}</span> ticket(s) visible(s)
            </p>
        </div>
    </div>
</template>
