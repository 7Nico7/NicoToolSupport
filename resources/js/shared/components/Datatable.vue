<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    // Definición de columnas
    // [{ key, label, sortable?, align?, width?, class? }]
    columns: { type: Array, required: true },

    // Filas de datos
    rows: { type: Array, default: () => [] },

    // Estado
    loading:   { type: Boolean, default: false },
    emptyText: { type: String,  default: 'Sin resultados' },
    emptyIcon: { type: String,  default: 'table_rows' },

    // Filas del skeleton mientras carga
    skeletonRows: { type: Number, default: 5 },

    // Selección
    selectable:  { type: Boolean, default: false },
    selectedRows:{ type: Array,   default: () => [] },

    // Row key
    rowKey: { type: String, default: 'id' },

    // Hover highlight
    hoverable: { type: Boolean, default: true },

    // Paginación (opcional, si pasan estos props se muestra)
    total:       { type: Number, default: null },
    currentPage: { type: Number, default: 1 },
    perPage:     { type: Number, default: 15 },
});

const emit = defineEmits(['sort', 'row-click', 'select', 'select-all', 'page-change']);

// Sorting local
const sortKey = ref(null);
const sortDir = ref('asc'); // asc | desc

const handleSort = (col) => {
    if (!col.sortable) return;
    if (sortKey.value === col.key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = col.key;
        sortDir.value = 'asc';
    }
    emit('sort', { key: sortKey.value, dir: sortDir.value });
};

const allSelected = computed(() =>
    props.rows.length > 0 && props.selectedRows.length === props.rows.length
);

const isSelected = (row) => props.selectedRows.includes(row[props.rowKey]);

const totalPages = computed(() =>
    props.total ? Math.ceil(props.total / props.perPage) : null
);
</script>

<template>
    <div class="flex flex-col gap-0">
        <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50">
                            <th v-if="selectable" class="w-10 px-4 py-3">
                                <input
                                    type="checkbox"
                                    :checked="allSelected"
                                    @change="emit('select-all', $event.target.checked)"
                                    class="rounded border-slate-300 text-brand focus:ring-brand/30"
                                />
                            </th>

                            <th
                                v-for="col in columns"
                                :key="col.key"
                                :class="[
                                    'px-4 py-3 text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap',
                                    col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left',
                                    col.sortable ? 'cursor-pointer select-none hover:text-slate-700 dark:hover:text-slate-200 transition-colors' : '',
                                    col.class ?? '',
                                ]"
                                @click="handleSort(col)"
                            >
                                <span class="inline-flex items-center gap-1">
                                    {{ col.label }}
                                    <span v-if="col.sortable" class="material-symbols-outlined text-[16px] opacity-60">
                                        {{ sortKey === col.key
                                            ? (sortDir === 'asc' ? 'arrow_upward' : 'arrow_downward')
                                            : 'unfold_more'
                                        }}
                                    </span>
                                </span>
                            </th>

                            <th v-if="$slots.actions" class="px-4 py-3 text-right w-24">
                                <span class="sr-only">Acciones</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        <template v-if="loading">
                            <tr v-for="i in skeletonRows" :key="`sk-${i}`">
                                <td v-if="selectable" class="px-4 py-3.5">
                                    <div class="h-4 w-4 rounded bg-slate-200 dark:bg-slate-700 animate-pulse" />
                                </td>
                                <td v-for="col in columns" :key="col.key" class="px-4 py-3.5">
                                    <div class="h-3.5 rounded-full bg-slate-200 dark:bg-slate-700 animate-pulse" :style="{ width: `${Math.random() * 40 + 40}%` }" />
                                </td>
                                <td v-if="$slots.actions" class="px-4 py-3.5">
                                    <div class="h-3.5 w-16 rounded-full bg-slate-200 dark:bg-slate-700 animate-pulse ml-auto" />
                                </td>
                            </tr>
                        </template>

                        <tr v-else-if="rows.length === 0">
                            <td :colspan="columns.length + (selectable ? 1 : 0) + ($slots.actions ? 1 : 0)" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl opacity-40">{{ emptyIcon }}</span>
                                    <p class="text-sm font-medium">{{ emptyText }}</p>
                                </div>
                            </td>
                        </tr>

                        <template v-else>
                            <tr
                                v-for="row in rows"
                                :key="row[rowKey]"
                                :class="[
                                    'transition-colors duration-100',
                                    hoverable ? 'hover:bg-slate-50 dark:hover:bg-slate-700/30' : '',
                                    isSelected(row) ? 'bg-brand/5 dark:bg-brand/10' : '',
                                ]"
                                @click="emit('row-click', row)"
                            >
                                <td v-if="selectable" class="px-4 py-3.5">
                                    <input
                                        type="checkbox"
                                        :checked="isSelected(row)"
                                        @click.stop
                                        @change="emit('select', { row, checked: $event.target.checked })"
                                        class="rounded border-slate-300 text-brand focus:ring-brand/30"
                                    />
                                </td>

                                <td
                                    v-for="col in columns"
                                    :key="col.key"
                                    :class="[
                                        'px-4 py-3.5 text-slate-700 dark:text-slate-300',
                                        col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left',
                                        col.class ?? '',
                                    ]"
                                >
                                    <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                        {{ row[col.key] ?? '—' }}
                                    </slot>
                                </td>

                                <td v-if="$slots.actions" class="px-4 py-3.5 text-right">
                                    <slot name="actions" :row="row" />
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div v-if="totalPages && totalPages > 1"
                 class="border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800/50 px-5 py-3">

                <div v-if="loading" class="flex items-center justify-between animate-pulse">
                    <div class="h-3 w-32 bg-slate-200 dark:bg-slate-700 rounded-full"></div>
                    <div class="flex gap-1">
                        <div v-for="i in 3" :key="i" class="h-8 w-8 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                    </div>
                </div>

                <div v-else class="flex items-center justify-between gap-4">
                    <div class="hidden sm:block">
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tight">
                            Mostrando <span class="text-slate-600 dark:text-slate-300">{{ (currentPage - 1) * perPage + 1 }}-{{ Math.min(currentPage * perPage, total) }}</span>
                            de <span class="text-slate-600 dark:text-slate-300">{{ total }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-1 w-full sm:w-auto justify-between sm:justify-end">
                        <button
                            @click="currentPage > 1 && emit('page-change', currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-20 disabled:pointer-events-none"
                        >
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </button>

                        <div class="hidden md:flex items-center gap-1">
                            <button
                                v-for="page in totalPages"
                                :key="page"
                                @click="emit('page-change', page)"
                                :class="[
                                    'min-w-[32px] h-8 px-2 rounded-lg text-xs font-black transition-all tabular-nums',
                                    page === currentPage
                                        ? 'bg-brand text-white shadow-sm shadow-brand/20'
                                        : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'
                                ]"
                            >{{ page }}</button>
                        </div>

                        <span class="md:hidden text-[11px] font-black text-slate-500">
                            {{ currentPage }} / {{ totalPages }}
                        </span>

                        <button
                            @click="currentPage < totalPages && emit('page-change', currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-20 disabled:pointer-events-none"
                        >
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
