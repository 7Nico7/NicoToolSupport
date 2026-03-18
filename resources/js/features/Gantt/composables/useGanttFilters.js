// resources/js/features/Gantt/composables/useGanttFilters.js
//
// ─────────────────────────────────────────────────────────────────────────────
// POR QUÉ ESTE COMPOSABLE EXISTE Y NO USA useFilters
//
//   El Gantt tiene dos tipos de "filtros" que se comportan diferente:
//
//   1. VENTANA DE TIEMPO (due_after, due_before)
//      — Controlada por navigate(prev/next), goToday() y changeZoom().
//      — Se aplica INMEDIATAMENTE y de forma INTENCIONAL (botones de navegación).
//      — Nunca se muestra como chip en el sidebar ni se cuenta como filtro activo.
//
//   2. FILTROS DEL SIDEBAR (status_id, priority_id, project_id, etc.)
//      — Controlados por SelectInput en el SidebarFilter.
//      — Se aplican inmediatamente al cambiar (sin debounce — el Gantt no
//        tiene paginación y la respuesta es rápida).
//      — Se muestran como chips removibles. Se cuentan en activeCount.
//
//   useFilters no puede manejar esto porque:
//     - Aplica debounce a TODO — no existe navegación intencional inmediata.
//     - Añade page: 1 — el Gantt no tiene paginación.
//     - No distingue entre ventana temporal y filtros de sidebar.
//
// USO ESTÁNDAR en Gantt/Index.vue:
//
//   const gf = useGanttFilters(props.filters, store)
//
//   gf.filters          → objeto reactivo completo (lectura/escritura)
//   gf.isOpen           → boolean reactivo del SidebarFilter
//   gf.activeCount      → número de filtros de sidebar activos
//   gf.windowLabel      → etiqueta del período actual ("Marzo 2026")
//   gf.navigate(dir)    → 'prev' | 'next'
//   gf.goToday()        → centra la ventana en hoy
//   gf.changeZoom(v)    → 'week' | 'month'
//   gf.updateFilter(k,v)→ cambia un filtro de sidebar y navega
//   gf.clearFilters()   → limpia solo los filtros de sidebar
//   gf.resolveLabel(k,v)→ texto legible para el chip de un filtro
// ─────────────────────────────────────────────────────────────────────────────

import { reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

// Claves que pertenecen al sidebar — nunca a la ventana temporal
const SIDEBAR_KEYS = ['status_id', 'priority_id', 'type_id', 'category_id', 'project_id', 'assigned_to', 'company_id']

export function useGanttFilters(initialFilters = {}, store, props = {}) {

    // ── Estado unificado ──────────────────────────────────────────────────────
    const state = reactive({
        isOpen: false,

        filters: {
            due_after:   initialFilters.due_after   ?? '',
            due_before:  initialFilters.due_before  ?? '',
            status_id:   initialFilters.status_id   ?? '',
            priority_id: initialFilters.priority_id ?? '',
            type_id:     initialFilters.type_id     ?? '',
            category_id: initialFilters.category_id ?? '',
            project_id:  initialFilters.project_id  ?? '',
            assigned_to: initialFilters.assigned_to ?? '',
            company_id:  initialFilters.company_id  ?? '',
        },

        // Solo cuenta los filtros de sidebar, no la ventana temporal
        activeCount: computed(() =>
            SIDEBAR_KEYS.filter(k => state.filters[k] !== '' && state.filters[k] !== null).length
        ),
    })

    // ── Aplicar — envía solo los valores no vacíos para mantener URL limpia ──
    // No añade page: 1 — el Gantt no tiene paginación
    // Vacía los valores '' y null para no contaminar la URL
    const _apply = () => {
        router.get(
            route('gantt'),
            Object.fromEntries(
                Object.entries(state.filters).filter(([, v]) => v !== '' && v !== null)
            ),
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }

    // ── Helpers de fecha ──────────────────────────────────────────────────────
    const _toDateStr = (d) => d.toISOString().split('T')[0]

    const _parseWindow = () => {
        const after  = state.filters.due_after  ? new Date(state.filters.due_after  + 'T00:00:00') : new Date()
        const before = state.filters.due_before ? new Date(state.filters.due_before + 'T00:00:00') : new Date()
        return { after, before }
    }

    // ── Etiqueta del período actual ───────────────────────────────────────────
    const windowLabel = computed(() => {
        const { after, before } = _parseWindow()
        if (store.zoom === 'week') {
            const startStr = after.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' })
            const endStr   = before.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
            return `${startStr} – ${endStr}`
        }
        const sameMonth = after.getMonth() === before.getMonth() && after.getFullYear() === before.getFullYear()
        if (sameMonth) return after.toLocaleDateString('es-MX', { month: 'long', year: 'numeric' })
        const startStr = after.toLocaleDateString('es-MX', { month: 'short' })
        const endStr   = before.toLocaleDateString('es-MX', { month: 'short', year: 'numeric' })
        return `${startStr} – ${endStr}`
    })

    // ── Navegación de la ventana temporal ─────────────────────────────────────
    const navigate = (direction) => {
        const { after, before } = _parseWindow()
        const sign = direction === 'next' ? 1 : -1
        let newAfter, newBefore

        if (store.zoom === 'week') {
            newAfter = new Date(after)
            newAfter.setDate(newAfter.getDate() + sign * 7)
            newBefore = new Date(newAfter)
            newBefore.setDate(newBefore.getDate() + 6)
        } else {
            newAfter  = new Date(after.getFullYear(), after.getMonth() + sign, 1)
            newBefore = new Date(newAfter.getFullYear(), newAfter.getMonth() + 1, 0)
        }

        state.filters.due_after  = _toDateStr(newAfter)
        state.filters.due_before = _toDateStr(newBefore)
        _apply()
    }

    const goToday = () => {
        const now = new Date()
        if (store.zoom === 'week') {
            const day = now.getDay() || 7
            const mon = new Date(now)
            mon.setDate(now.getDate() - day + 1)
            const sun = new Date(mon)
            sun.setDate(mon.getDate() + 6)
            state.filters.due_after  = _toDateStr(mon)
            state.filters.due_before = _toDateStr(sun)
        } else {
            state.filters.due_after  = _toDateStr(new Date(now.getFullYear(), now.getMonth(), 1))
            state.filters.due_before = _toDateStr(new Date(now.getFullYear(), now.getMonth() + 1, 0))
        }
        _apply()
    }

    const changeZoom = (value) => {
        store.setZoom(value)
        const { after } = _parseWindow()
        const anchor = after ?? new Date()

        if (value === 'week') {
            const day = anchor.getDay() || 7
            const mon = new Date(anchor)
            mon.setDate(anchor.getDate() - day + 1)
            const sun = new Date(mon)
            sun.setDate(mon.getDate() + 6)
            state.filters.due_after  = _toDateStr(mon)
            state.filters.due_before = _toDateStr(sun)
        } else {
            state.filters.due_after  = _toDateStr(new Date(anchor.getFullYear(), anchor.getMonth(), 1))
            state.filters.due_before = _toDateStr(new Date(anchor.getFullYear(), anchor.getMonth() + 1, 0))
        }
        _apply()
    }

    // ── Filtros del sidebar ───────────────────────────────────────────────────

    // Un solo método para cambiar cualquier filtro de sidebar desde el template
    //    — reemplaza el patrón anterior de @update:model-value="v => updateFilter('key', v)"
    const updateFilter = (key, val) => {
        state.filters[key] = val ?? ''
        _apply()
    }

    // clear solo limpia los filtros de sidebar, no la ventana temporal
    //    — clearFilters() NO cierra el sidebar (lo hace el componente con @clear)
    const clearFilters = () => {
        SIDEBAR_KEYS.forEach(k => { state.filters[k] = '' })
        _apply()
    }

    // resolveLabel centralizado — antes estaba inline en Index.vue
    const resolveLabel = (key, value) => {
        const maps = {
            company_id:  props.companies,
            project_id:  props.projects,
            status_id:   props.statuses,
            priority_id: props.priorities,
            assigned_to: props.agents,
            type_id:     props.types,
            category_id: props.categories,
        }
        return maps[key]?.find(item => item.id == value)?.name ?? value
    }

    // ── API pública ───────────────────────────────────────────────────────────
    return {
        ...state,       // filters, isOpen, activeCount (reactivos)
        windowLabel,    // computed
        navigate,
        goToday,
        changeZoom,
        updateFilter,
        clearFilters,
        resolveLabel,
    }
}
