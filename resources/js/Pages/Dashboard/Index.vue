<script setup>
// resources/js/Pages/Dashboard/Index.vue
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useAuth } from '@/shared/composables/useAuth'
import { useDarkMode } from '@/shared/composables/useDarkMode'

// ── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps({
    dashboard: { type: Object, required: true },
})

const { authUser } = useAuth()
const { isDark }   = useDarkMode()
const d            = computed(() => props.dashboard)

// ── Helpers ───────────────────────────────────────────────────────────────────
const fmtHours = (h) => {
    if (!h) return '—'
    if (h < 1) return `${Math.round(h * 60)} min`
    if (h < 24) return `${h}h`
    return `${(h / 24).toFixed(1)} días`
}

// ── Chart.js — donut estado ───────────────────────────────────────────────────
const donutCanvas = ref(null)
let donutChart    = null

const buildDonut = async () => {
    if (!donutCanvas.value || !d.value.by_status?.length) return
    const { Chart, ArcElement, DoughnutController, Tooltip, Legend } = await import('chart.js')
    Chart.register(ArcElement, DoughnutController, Tooltip, Legend)

    if (donutChart) { donutChart.destroy(); donutChart = null }

    donutChart = new Chart(donutCanvas.value, {
        type: 'doughnut',
        data: {
            labels:   d.value.by_status.map(s => s.name),
            datasets: [{
                data:            d.value.by_status.map(s => s.count),
                backgroundColor: d.value.by_status.map(s => s.color + 'CC'),
                borderColor:     d.value.by_status.map(s => s.color),
                borderWidth:     2,
                hoverOffset:     6,
            }],
        },
        options: {
            cutout:     '72%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.parsed} tickets`,
                    },
                },
            },
        },
    })
}

// ── Chart.js — tendencia ──────────────────────────────────────────────────────
const trendCanvas = ref(null)
let trendChart    = null

const buildTrend = async () => {
    if (!trendCanvas.value || !d.value.trend?.length) return
    const { Chart, LineElement, PointElement, LinearScale, CategoryScale,
            LineController, Filler, Tooltip } = await import('chart.js')
    Chart.register(LineElement, PointElement, LinearScale, CategoryScale,
                   LineController, Filler, Tooltip)

    if (trendChart) { trendChart.destroy(); trendChart = null }

    const gridColor  = isDark.value ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)'
    const labelColor = isDark.value ? '#94a3b8' : '#64748b'

    trendChart = new Chart(trendCanvas.value, {
        type: 'line',
        data: {
            labels:   d.value.trend.map(t => t.label),
            datasets: [{
                label:           'Tickets creados',
                data:            d.value.trend.map(t => t.count),
                borderColor:     '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.12)',
                borderWidth:     2.5,
                pointRadius:     3,
                pointBackgroundColor: '#6366f1',
                tension:         0.4,
                fill:            true,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: {
                x: {
                    grid:  { color: gridColor },
                    ticks: { color: labelColor, font: { size: 11 } },
                },
                y: {
                    beginAtZero: true,
                    grid:  { color: gridColor },
                    ticks: { color: labelColor, precision: 0, font: { size: 11 } },
                },
            },
        },
    })
}

onMounted(() => { buildDonut(); buildTrend() })
onUnmounted(() => { donutChart?.destroy(); trendChart?.destroy() })
watch(isDark, () => { buildDonut(); buildTrend() })

// ── Colores de prioridad ──────────────────────────────────────────────────────
const priorityWidth = (count) => {
    const max = Math.max(...(d.value.by_priority?.map(p => p.count) ?? [1]))
    return max ? `${Math.round((count / max) * 100)}%` : '0%'
}
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">

            <!-- ── Page header ────────────────────────────────────────────── -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Dashboard</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 capitalize">
                        {{ authUser?.name }} ·
                        <span class="font-semibold text-brand">{{ authUser?.role?.replace('_', ' ') }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('kanban')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest
                               bg-brand text-white shadow-lg shadow-brand/20 hover:scale-[1.02] transition-all">
                        <span class="material-symbols-outlined text-[16px]">view_kanban</span>
                        Ir al Kanban
                    </Link>
                </div>
            </div>

            <!-- ── Stat cards ─────────────────────────────────────────────── -->
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">

                <!-- Total -->
                <div class="col-span-1 flex flex-col gap-2 p-5 rounded-2xl bg-white dark:bg-slate-800
                            border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-slate-500 text-[18px]">confirmation_number</span>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-slate-900 dark:text-white">{{ d.stats.total }}</p>
                    <p class="text-[11px] text-slate-400">tickets registrados</p>
                </div>

                <!-- Abiertos -->
                <div class="col-span-1 flex flex-col gap-2 p-5 rounded-2xl bg-white dark:bg-slate-800
                            border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Abiertos</span>
                        <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-amber-500 text-[18px]">pending</span>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-slate-900 dark:text-white">{{ d.stats.open }}</p>
                    <p class="text-[11px] text-slate-400">sin cerrar</p>
                </div>

                <!-- Cerrados -->
                <div class="col-span-1 flex flex-col gap-2 p-5 rounded-2xl bg-white dark:bg-slate-800
                            border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Cerrados</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-emerald-500 text-[18px]">check_circle</span>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-slate-900 dark:text-white">{{ d.stats.closed }}</p>
                    <p class="text-[11px] text-slate-400">resueltos</p>
                </div>

                <!-- Vencidos -->
                <div class="col-span-1 flex flex-col gap-2 p-5 rounded-2xl shadow-sm"
                    :class="d.stats.overdue > 0
                        ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'
                        : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700'">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-widest"
                            :class="d.stats.overdue > 0 ? 'text-red-500' : 'text-slate-400'">Vencidos</span>
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center"
                            :class="d.stats.overdue > 0 ? 'bg-red-100 dark:bg-red-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                            <span class="material-symbols-outlined text-[18px]"
                                :class="d.stats.overdue > 0 ? 'text-red-500' : 'text-slate-400'">warning</span>
                        </span>
                    </div>
                    <p class="text-3xl font-black"
                        :class="d.stats.overdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white'">
                        {{ d.stats.overdue }}
                    </p>
                    <p class="text-[11px]" :class="d.stats.overdue > 0 ? 'text-red-400' : 'text-slate-400'">
                        pasaron su fecha límite
                    </p>
                </div>

                <!-- Tiempo promedio -->
                <div class="col-span-1 flex flex-col gap-2 p-5 rounded-2xl bg-white dark:bg-slate-800
                            border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-violet-500 uppercase tracking-widest">Resolución</span>
                        <span class="w-8 h-8 rounded-xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-violet-500 text-[18px]">timer</span>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-slate-900 dark:text-white">
                        {{ fmtHours(d.avg_resolution_hours) }}
                    </p>
                    <p class="text-[11px] text-slate-400">promedio de cierre</p>
                </div>

            </div>

            <!-- ── Fila 2: Donut + Tendencia ──────────────────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Distribución por estado (donut) -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">
                        Por estado
                    </p>
                    <div class="flex items-center gap-5">
                        <div class="relative w-36 h-36 shrink-0">
                            <canvas ref="donutCanvas" />
                            <!-- Centro del donut -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ d.stats.total }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">tickets</span>
                            </div>
                        </div>
                        <!-- Leyenda -->
                        <ul class="flex-1 space-y-2">
                            <li v-for="s in d.by_status" :key="s.name" class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ background: s.color }" />
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate">{{ s.name }}</span>
                                </div>
                                <span class="text-xs font-black text-slate-900 dark:text-white shrink-0">{{ s.count }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tendencia últimos 14 días -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">
                        Tickets creados — últimos 14 días
                    </p>
                    <div class="h-44">
                        <canvas ref="trendCanvas" />
                    </div>
                </div>
            </div>

            <!-- ── Fila 3: Prioridad + Agentes/Compañías ─────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Por prioridad (barras horizontales) -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">
                        Por prioridad
                    </p>
                    <ul class="space-y-3">
                        <li v-for="p in d.by_priority" :key="p.name">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ p.name }}</span>
                                <span class="text-xs font-black text-slate-900 dark:text-white">{{ p.count }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                    :style="{ width: priorityWidth(p.count), background: p.color }" />
                            </div>
                        </li>
                    </ul>

                    <!-- Por vencer pronto -->
                    <div v-if="d.stats.dueSoon > 0"
                        class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 text-[18px]">schedule</span>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            <span class="font-black text-amber-500">{{ d.stats.dueSoon }}</span>
                            tickets vencen en los próximos 3 días
                        </p>
                    </div>
                </div>

                <!-- Por agente (admin / super_admin) -->
                <div v-if="d.by_agent?.length"
                    class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">
                        Carga por agente
                    </p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-left">
                                    <th class="pb-3 font-black text-slate-400 uppercase tracking-widest pr-4">Agente</th>
                                    <th class="pb-3 font-black text-slate-400 uppercase tracking-widest text-center w-20">Abiertos</th>
                                    <th class="pb-3 font-black text-slate-400 uppercase tracking-widest text-center w-20">Cerrados</th>
                                    <th class="pb-3 font-black text-slate-400 uppercase tracking-widest text-center w-20">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <tr v-for="a in d.by_agent" :key="a.name" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-2.5 pr-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-[11px] font-black shrink-0">
                                                {{ a.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ a.name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400">
                                            {{ a.open }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400">
                                            {{ a.closed }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-center font-black text-slate-900 dark:text-white">{{ a.total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Por compañía (solo super_admin) -->
                <div v-else-if="d.by_company?.length"
                    class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">
                        Tickets por compañía
                    </p>
                    <ul class="space-y-3">
                        <li v-for="c in d.by_company" :key="c.name" class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-brand/10 text-brand flex items-center justify-center text-[11px] font-black shrink-0">
                                {{ c.name.charAt(0) }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate">{{ c.name }}</span>
                                    <span class="text-xs font-black text-slate-900 dark:text-white ml-2 shrink-0">{{ c.count }}</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                    <div class="h-full rounded-full bg-brand transition-all duration-500"
                                        :style="{ width: priorityWidth(c.count) }" />
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- ── Tickets recientes ───────────────────────────────────────── -->
            <div v-if="d.recent_tickets?.length"
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                        Tickets recientes
                    </p>
                    <Link :href="route('kanban')"
                        class="text-[10px] font-black text-brand uppercase tracking-widest hover:underline">
                        Ver todos →
                    </Link>
                </div>
                <div class="space-y-2">
                    <div v-for="t in d.recent_tickets" :key="t.id"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <span class="text-[10px] font-black text-brand bg-brand/10 px-2 py-0.5 rounded-lg tracking-widest shrink-0">
                            {{ t.ticket_number }}
                        </span>
                        <p class="flex-1 text-sm font-semibold text-slate-800 dark:text-slate-200 truncate min-w-0">
                            {{ t.title }}
                        </p>
                        <div class="flex items-center gap-2 shrink-0">
                            <span v-if="t.status"
                                class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black border border-current/10"
                                :style="{ color: t.status.color, background: t.status.color + '15' }">
                                {{ t.status.name }}
                            </span>
                            <span v-if="t.priority"
                                class="w-2 h-2 rounded-full shrink-0"
                                :style="{ background: t.priority.color }"
                                :title="t.priority.name" />
                            <span class="text-[10px] text-slate-400 hidden md:block">{{ t.created_at }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
