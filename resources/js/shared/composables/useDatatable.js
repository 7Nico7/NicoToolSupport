// resources/js/shared/composables/useDatatable.js
//
// ─────────────────────────────────────────────────────────────────────────────
// PROPÓSITO
//   Encapsula la lógica de paginación para el componente <Datatable>.
//   Garantiza que TODOS los módulos usen Datatable de la misma forma,
//   eliminando la posibilidad de lambdas inline, acceso directo a props de
//   Laravel Pagination o lógica duplicada de router.get().
//
// USO ESTÁNDAR (el único permitido):
//
//   // 1. En <script setup>
//   const dt = useDatatable('users.index', () => props.users, filterManager)
//
//   // 2. En el template — siempre spread con v-bind + @page-change
//   <Datatable v-bind="dt.bind" :columns="columns" @page-change="dt.changePage">
//     <template #cell-name="{ row }"> ... </template>
//     <template #actions="{ row }"> ... </template>
//   </Datatable>
//
// PARÁMETROS
//   routeName     — nombre de la ruta Inertia (ej. 'users.index')
//   resourceFn    — función que retorna el objeto paginado de Laravel
//                   ({ data, total, current_page, per_page, ... })
//                   Puede ser una arrow function que lea props reactivos.
//   filterManager — (opcional) objeto retornado por useFilters().
//                   Si se pasa, sus filtros se incluyen en cada navegación.
//   options       — (opcional) overrides de props visuales del Datatable
//                   { emptyText, emptyIcon, loading, skeletonRows, ... }
// ─────────────────────────────────────────────────────────────────────────────

import { computed, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

/**
 * @param {string}     routeName
 * @param {()=>object} resourceFn     Función que retorna el paginador de Laravel
 * @param {object}     [filterManager] Retorno de useFilters()
 * @param {object}     [options]       Props visuales opcionales para <Datatable>
 */
export function useDatatable(routeName, resourceFn, filterManager = null, options = {}) {

    const resource = computed(resourceFn)

    // ── Props de datos — extraídos del paginador de Laravel ──────────────────
    const rows        = computed(() => resource.value?.data          ?? [])
    const total       = computed(() => resource.value?.total         ?? 0)
    const currentPage = computed(() => resource.value?.current_page  ?? 1)
    const perPage     = computed(() => resource.value?.per_page      ?? 15)

    // ── Paginación ────────────────────────────────────────────────────────────
    const changePage = (page) => {
        const params = filterManager
            ? { ...filterManager.filters, page }
            : { page }

        router.get(route(routeName, params), {}, { preserveState: true })
    }

    // ── bind — objeto listo para hacer v-bind en el template ─────────────────
    //
    //  POR QUÉ reactive() Y NO computed():
    //
    //    v-bind="dt.bind" en el template evalúa dt.bind como expresión ANIDADA.
    //    Vue solo auto-desenvuelve refs TOP-LEVEL del contexto del template.
    //    Como dt es un objeto plano (no reactivo), dt.bind NO se desenvuelve
    //    automáticamente — v-bind recibiría el ComputedRef en sí mismo
    //    (con sus propiedades internas __v_isRef, value, effect...)
    //    en lugar de los datos reales: resultado = tabla vacía, sin errores.
    //
    //    Con reactive(), v-bind recibe el proxy reactivo y Vue lo esparce
    //    correctamente. Los computed refs dentro de reactive() se desenvuelven
    //    al accederlos, por lo que rows, total, etc. llegan como valores.
    //
    const bind = reactive({
        rows,         // computed — reactive() lo desenvuelve al accederlo
        total,        // computed — reactive() lo desenvuelve al accederlo
        currentPage,  // computed — reactive() lo desenvuelve al accederlo
        perPage,      // computed — reactive() lo desenvuelve al accederlo
        ...options,   // emptyText, emptyIcon, loading, etc.
    })

    return {
        // Para uso con v-bind (recomendado — evita olvidar props)
        bind,

        // Para uso individual si se necesita acceder a un valor puntual
        rows,
        total,
        currentPage,
        perPage,

        // Handler de paginación — siempre igual, nunca lambda inline
        changePage,
    }
}
