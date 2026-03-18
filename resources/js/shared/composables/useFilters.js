// resources/js/shared/composables/useFilters.js
//
// ─────────────────────────────────────────────────────────────────────────────
// USO ESTÁNDAR — la única forma permitida en todos los módulos:
//
//   const filterManager = useFilters('users.index', {
//       search:    props.filters.search    ?? '',
//       role:      props.filters.role      ?? '',
//       is_active: props.filters.is_active ?? '',
//   })
//
//   Leer/escribir filtros    → filterManager.filters.search = 'algo'
//   Contar filtros activos   → filterManager.activeCount
//   Abrir / cerrar sidebar   → filterManager.isOpen = true/false
//   Limpiar todos            → filterManager.clear()
//   Aplicar manualmente      → filterManager.apply()
//
// El watch interno aplica con debounce (400ms) en cada cambio de filtros,
// así que en la mayoría de casos no necesitas llamar apply() desde el template.
//
// PARÁMETROS
//   routeName      — nombre de la ruta Inertia  (ej. 'users.index')
//   initialFilters — valores iniciales sincronizados desde props.filters
// ─────────────────────────────────────────────────────────────────────────────

import { reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

export function useFilters(routeName, initialFilters = {}) {

    // Un único reactive que agrupa todo el estado — los métodos se definen
    // como propiedades del mismo objeto para poder referenciarlo internamente
    // sin necesidad de variables intermedias ni closures adicionales.
    const filterManager = reactive({

        // ── Estado ────────────────────────────────────────────────────────────
        isOpen:  false,                    // UI: abre/cierra el SidebarFilter
        filters: { ...initialFilters },    // Los filtros reales

        // computed dentro de reactive se desenvuelve automáticamente —
        //    filterManager.activeCount devuelve el número, no el ComputedRef
        activeCount: computed(() =>
            Object.values(filterManager.filters)
                .filter(v => v !== '' && v !== null && v !== undefined)
                .length
        ),

        // ── Aplicar ───────────────────────────────────────────────────────────
        //  page: 1 siempre al cambiar filtros — evita quedar en una página
        //    que no existe en los nuevos resultados filtrados.
        //  params dentro de route() — consistente con useDatatable.changePage
        //    y compatible con rutas que tienen segmentos obligatorios (/company/{id}/users).
        apply() {
            router.get(
                route(routeName, { ...filterManager.filters, page: 1 }),
                {},
                { preserveState: true, replace: true, preserveScroll: true }
            )
        },

        // Exportada correctamente — en la versión anterior era función local
        //    y nunca salía del composable, causando "clear is not a function".
        // Restaura los valores de initialFilters (no limpia a string vacío
        // si el valor inicial era null u otro valor específico).
        clear() {
            Object.keys(filterManager.filters).forEach(key => {
                filterManager.filters[key] = initialFilters[key] ?? ''
            })
            // apply() se dispara automáticamente por el watch — no hace falta llamarlo
        },
    })

    // ── Watch debounced ───────────────────────────────────────────────────────
    // Observa filters directamente (no isOpen — los cambios de UI no navegan).
    // deep: true detecta cambios en propiedades anidadas del objeto de filtros.
    let debounceTimer = null

    watch(
        () => filterManager.filters,
        () => {
            clearTimeout(debounceTimer)
            debounceTimer = setTimeout(() => filterManager.apply(), 400)
        },
        { deep: true }
    )

    return filterManager
}
