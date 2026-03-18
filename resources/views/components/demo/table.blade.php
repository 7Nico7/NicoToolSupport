@props([
    'items' => [],
    'columns' => [],
    'title' => null,
    'searchPlaceholder' => 'Buscar...',
    'emptyMessage' => 'Sin registros',
    'itemsPerPage' => 5,
])

@php
    $items = collect($items);
@endphp

<div x-data="tablePagination({
    totalItems: {{ $items->count() }},
    perPage: {{ $itemsPerPage }}
})"
    class="bg-surface border border-gray-200 dark:border-white/[0.08] rounded-xl shadow-sm overflow-hidden">

    {{-- HEADER --}}
    <div
        class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 gap-4 border-b border-gray-200 dark:border-white/[0.08]">
        <div class="flex items-center gap-4">
            @if ($title)
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-1.5 h-5 bg-brand rounded-full"></span>
                    {{ $title }}
                </h3>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            {{-- BUSCADOR --}}
            <div class="relative w-full sm:w-64">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                <input type="text" placeholder="{{ $searchPlaceholder }}"
                    class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.1] rounded-lg focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all dark:text-gray-200 dark:placeholder:text-gray-500"
                    @input="filterTable($event.target.value)">
            </div>

            {{-- AQUÍ SE RENDERIZA EL BOTÓN QUE PASASTE POR EL SLOT --}}
            @if (isset($actions))
                <div class="w-full sm:w-auto">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border-collapse">
            <thead class="bg-gray-50/50 dark:bg-white/[0.02] border-b border-gray-200 dark:border-white/[0.08]">
                <tr>
                    @foreach ($columns as $column)
                        @if ($column['visible'] ?? true)
                            <th
                                class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $column['label'] }}
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>

            <tbody id="demoTableBody" class="divide-y divide-gray-200 dark:divide-white/[0.05]">
                @forelse($items as $index => $item)
                    <tr x-show="rowMatchesSearch({{ $index }}) && isRowInCurrentPage({{ $index }})"
                        class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors group"
                        data-index="{{ $index }}" data-search-text="{{ strtolower(json_encode($item)) }}">
                        @foreach ($columns as $column)
                            @if ($column['visible'] ?? true)
                                @php $value = data_get($item, $column['field']); @endphp
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    @switch($column['format'] ?? null)
                                        @case('currency')
                                            <span
                                                class="font-semibold text-gray-900 dark:text-white">${{ number_format($value, 2) }}</span>
                                        @break

                                        @case('badge')
                                            @php
                                                $color = match (strtolower($value)) {
                                                    'aprobada',
                                                    'activo',
                                                    'completado'
                                                        => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                    'pendiente'
                                                        => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                                    default
                                                        => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-400',
                                                };
                                            @endphp
                                            <span
                                                class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase {{ $color }}">{{ $value }}</span>
                                        @break

                                        @case('code')
                                            <span
                                                class="font-mono text-[12px] bg-gray-100 dark:bg-white/[0.08] text-brand px-2 py-0.5 rounded border border-gray-200 dark:border-white/[0.1]">{{ $value }}</span>
                                        @break

                                        @default
                                            {{ $value }}
                                    @endswitch
                                </td>
                            @endif
                        @endforeach
                    </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="text-center py-12 text-gray-400">Sin datos</td>
                        </tr>
                    @endforelse

                    {{-- Mensaje cuando la búsqueda no da resultados --}}
                    <tr x-show="filteredTotal === 0" x-cloak>
                        <td colspan="{{ count($columns) }}" class="text-center py-12 text-gray-400 italic">
                            No se encontraron resultados para la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- FOOTER / PAGINACIÓN --}}
        <div
            class="flex items-center justify-between px-4 py-3 bg-gray-50/50 dark:bg-white/[0.02] border-t border-gray-200 dark:border-white/[0.08]">
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Mostrando <span class="font-bold text-gray-700 dark:text-gray-200" x-text="startRange"></span>
                        a <span class="font-bold text-gray-700 dark:text-gray-200" x-text="endRange"></span>
                        de <span class="font-bold text-gray-700 dark:text-gray-200" x-text="filteredTotal"></span> registros
                    </p>
                </div>

                <div class="flex gap-1" x-show="totalPages > 1">
                    <button @click="prevPage()" :disabled="currentPage === 1"
                        class="p-1.5 rounded-lg border border-gray-200 dark:border-white/[0.1] hover:bg-white dark:hover:bg-white/[0.05] disabled:opacity-30 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>

                    <template x-for="p in totalPages" :key="p">
                        <button @click="currentPage = p"
                            :class="currentPage === p ? 'bg-brand text-white border-brand' :
                                'text-gray-500 dark:text-gray-400 border-gray-200 dark:border-white/[0.1] hover:bg-white dark:hover:bg-white/[0.05]'"
                            class="w-8 h-8 text-xs font-bold rounded-lg border transition-all" x-text="p">
                        </button>
                    </template>

                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                        class="p-1.5 rounded-lg border border-gray-200 dark:border-white/[0.1] hover:bg-white dark:hover:bg-white/[0.05] disabled:opacity-30 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function tablePagination(config) {
            return {
                currentPage: 1,
                perPage: config.perPage,
                filteredTotal: config.totalItems,
                searchQuery: '',
                matches: [], // Guardará los índices de las filas que coinciden con la búsqueda

                init() {
                    this.updateMatches();
                },

                get totalPages() {
                    return Math.ceil(this.filteredTotal / this.perPage);
                },
                get startRange() {
                    return this.filteredTotal === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
                },
                get endRange() {
                    return Math.min(this.currentPage * this.perPage, this.filteredTotal);
                },
                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },
                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },

                // Verifica si la fila coincide con el texto de búsqueda
                rowMatchesSearch(index) {
                    return this.matches.includes(index);
                },

                // Verifica si la fila (dentro de las filtradas) pertenece a la página actual
                isRowInCurrentPage(index) {
                    const positionInFiltered = this.matches.indexOf(index);
                    if (positionInFiltered === -1) return false;

                    const start = (this.currentPage - 1) * this.perPage;
                    const end = start + this.perPage;
                    return positionInFiltered >= start && positionInFiltered < end;
                },

                filterTable(query) {
                    this.searchQuery = query.toLowerCase();
                    this.currentPage = 1; // Resetear a pag 1 al buscar
                    this.updateMatches();
                },

                updateMatches() {
                    const rows = document.querySelectorAll("#demoTableBody tr[data-search-text]");
                    const newMatches = [];

                    rows.forEach(row => {
                        const text = row.getAttribute('data-search-text');
                        const index = parseInt(row.getAttribute('data-index'));
                        if (text.includes(this.searchQuery)) {
                            newMatches.push(index);
                        }
                    });

                    this.matches = newMatches;
                    this.filteredTotal = newMatches.length;
                }
            }
        }
    </script>
