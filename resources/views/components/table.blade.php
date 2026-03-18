@props([
    'items' => null,
    'columns' => [],
    'actions' => [],
    'title' => null,
    'createRoute' => null,
    'filters' => [],
    'searchPlaceholder' => 'Buscar...',
    'emptyMessage' => 'No hay registros para mostrar',
    'filtersData' => [],
    'tableId' => 'table-' . uniqid(),
    'exportRoute' => null,
    'enableColumnManager' => true,
    'enableFilters' => true,
    'enableDragAndDrop' => true,
])

@php
    $isPaginated = $items instanceof \Illuminate\Pagination\LengthAwarePaginator;
    $currentItems = $isPaginated ? $items->items() : $items;
    $total = $isPaginated ? $items->total() : $items->count();
    $firstItem = $isPaginated ? $items->firstItem() : 1;
    $lastItem = $isPaginated ? $items->lastItem() : $total;

    // Preparar columnas para el gestor
    $columnFields = [];
    foreach ($columns as $column) {
        $columnFields[] = [
            'field' => $column['field'] ?? '',
            'label' => $column['label'] ?? '',
            'visible' => $column['visible'] ?? true,
            'width' => $column['width'] ?? null,
        ];
    }
@endphp

<!-- Filters and Actions -->
<div class="row mb-3 g-3">
    <div class="col-md-6">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2">
            @foreach (request()->except(['search', 'page']) as $key => $value)
                @if (!is_array($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <div class="flex-grow-1">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0"
                        placeholder="{{ $searchPlaceholder }}" value="{{ request('search') }}">
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end gap-2">
            @if ($createRoute)
                <a href="{{ $createRoute }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Agregar
                </a>
            @endif

            <button class="btn btn-outline-secondary" type="button" onclick="window.location.reload()"
                title="Recargar">
                <i class="bi bi-arrow-clockwise"></i>
            </button>

            @if ($exportRoute)
                <button class="btn btn-outline-success" type="button"
                    onclick="tableManager.exportToExcel('{{ $exportRoute }}')" title="Exportar Excel">
                    <i class="bi bi-file-earmark-excel"></i>
                </button>
            @endif

            @if ($enableFilters && !empty($filters))
                <button class="btn btn-outline-primary" type="button" onclick="tableManager.showFiltersSidebar()"
                    title="Filtros avanzados">
                    <i class="bi bi-funnel"></i>
                    <span id="activeFiltersBadge" class="badge bg-danger rounded-pill d-none"
                        style="position: absolute; top: -5px; right: -5px; font-size: 10px;">0</span>
                </button>
            @endif

            @if ($enableColumnManager)
                <button class="btn btn-outline-secondary" type="button" onclick="tableManager.showColumnsSidebar()"
                    title="Personalizar columnas">
                    <i class="bi bi-columns-gap"></i>
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <!-- Table Header -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        {{ $title ?? 'Listado' }}
                    </h6>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-light text-dark" id="rowCounter">
                            {{ $total }} registros
                        </span>
                    </div>
                </div>
            </div>

            <!-- Table Body -->
            <div class="card-body p-0">
                @if ($total > 0)
                    <div class="table-wrapper">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="{{ $tableId }}"
                                data-table-manager="true">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        @if (!empty($actions))
                                            <th class="ps-4" width="50" data-db-field="actions"
                                                data-field="actions">
                                                <i class="bi bi-gear text-muted"></i>
                                            </th>
                                        @endif

                                        @foreach ($columns as $column)
                                            @php
                                                $field = $column['field'] ?? '';
                                                $label = $column['label'] ?? ucfirst($field);
                                                $width = $column['width'] ?? null;
                                                $align = $column['align'] ?? 'left';
                                            @endphp

                                            <th class="fw-semibold text-{{ $align }} {{ $column['class'] ?? '' }}"
                                                @if ($width) width="{{ $width }}" @endif
                                                data-db-field="{{ $field }}" data-field="{{ $field }}">
                                                {{ $label }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @foreach ($currentItems as $item)
                                        <tr class="border-bottom">
                                            @if (!empty($actions))
                                                <td class="ps-4" data-field="actions">
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-sm btn-ghost-secondary dropdown-toggle p-0"
                                                            data-bs-toggle="dropdown"
                                                            style="background: none; border: none;">
                                                            <i class="bi bi-three-dots-vertical text-muted"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @foreach ($actions as $action)
                                                                @if (($action['method'] ?? '') == 'DIVIDER')
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                @else
 @php
    // Evaluar label (puede ser string o Closure)
    $label = $action['label'] ?? '';
    if (is_callable($label)) {
        $label = $label($item);
    }

    // Evaluar icon (puede ser string o Closure)
    $icon = $action['icon'] ?? 'trash';
    if (is_callable($icon)) {
        $icon = $icon($item);
    }

    // Evaluar method (puede ser string o Closure)
    $method = $action['method'] ?? 'GET';
    if (is_callable($method)) {
        $method = $method($item);
    }

    // Evaluar class (puede ser string o Closure)
    $class = $action['class'] ?? '';
    if (is_callable($class)) {
        $class = $class($item);
    }

    // Construir la URL
    $url = '#';
    if (isset($action['route'])) {
        // Usar route() con el nombre de la ruta y el parámetro
        $params = isset($action['parameter']) ? [$action['parameter'] => $item->id] : [$item->id];
        $url = route($action['route'], $params);
    } elseif (isset($action['url'])) {
        // Compatibilidad con el formato anterior (URL directa)
        $url = $action['url'];
        if (is_callable($url)) {
            $url = $url($item);
        } elseif (is_string($url) && str_contains($url, '{id}')) {
            $url = str_replace('{id}', $item->id, $url);
        }
    }
@endphp

                                                                    @if ($method == 'DELETE')
                                                                        <li>
                                                                            <form action="{{ $url }}"
                                                                                method="POST"
                                                                                class="d-inline delete-form">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="button"
                                                                                    class="dropdown-item text-danger delete-btn {{ $class }}"
                                                                                    data-name="{{ $item->nombre ?? ($item->name ?? '') }}">
                                                                                    <i
                                                                                        class="bi bi-{{ $icon }} me-2"></i>
                                                                                    {{ $label }}
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                    @else
                                                                        <li>
                                                                            <a class="dropdown-item {{ $class }}"
                                                                                href="{{ $url }}">
                                                                                @if ($icon)
                                                                                    <i
                                                                                        class="bi bi-{{ $icon }} me-2"></i>
                                                                                @endif
                                                                                {{ $label }}
                                                                            </a>
                                                                        </li>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif

                                            @foreach ($columns as $column)
                                                @php
                                                    $field = $column['field'] ?? '';
                                                    $align = $column['align'] ?? 'left';
                                                    $format = $column['format'] ?? null;
                                                @endphp

                                                <td class="text-{{ $align }} {{ $column['tdClass'] ?? '' }}"
                                                    data-field="{{ $field }}">

                                                    @if (isset($column['format']))
                                                        @switch($column['format'])
                                                            @case('currency')
                                                                <span
                                                                    class="{{ $column['valueClass'] ?? 'text-success fw-bold small' }}">
                                                                    ${{ number_format(data_get($item, $field), 2) }}
                                                                </span>
                                                            @break

                                                            @case('currency_alt')
                                                                <span
                                                                    class="{{ $column['valueClass'] ?? 'text-muted small' }}">
                                                                    {{ data_get($item, $field) ? '$' . number_format(data_get($item, $field), 2) : '-' }}
                                                                </span>
                                                            @break

                                                            @case('currency_cost')
                                                                <span
                                                                    class="{{ $column['valueClass'] ?? 'text-danger small' }}">
                                                                    ${{ number_format(data_get($item, $field), 2) }}
                                                                </span>
                                                            @break

                                                            @case('code')
                                                                <span
                                                                    class="{{ $column['valueClass'] ?? 'text-dark font-monospace small' }}">
                                                                    {{ data_get($item, $field) }}
                                                                </span>
                                                            @break

                                                            @case('code_muted')
                                                                <code
                                                                    class="{{ $column['valueClass'] ?? 'text-muted small' }}">
                                                                    {{ data_get($item, $field) ?: '-' }}
                                                                </code>
                                                            @break

                                                            @case('text_muted')
                                                                <span
                                                                    class="{{ $column['valueClass'] ?? 'text-muted small' }}">
                                                                    {{ data_get($item, $field) ?: '-' }}
                                                                </span>
                                                            @break

                                                            @case('text_limit')
                                                                <small class="{{ $column['valueClass'] ?? 'text-muted' }}">
                                                                    {{ Str::limit(data_get($item, $field), $column['limit'] ?? 50) ?: '-' }}
                                                                </small>
                                                            @break

                                                            @case('badge_boolean')
                                                                @if (data_get($item, $field))
                                                                    <span
                                                                        class="badge bg-success-subtle text-success border border-success-subtle">
                                                                        <i class="bi bi-check-circle-fill small"></i>
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                                        <i class="bi bi-x-circle-fill small"></i>
                                                                    </span>
                                                                @endif
                                                            @break

                                                            @case('badge_favorite')
                                                                @if (data_get($item, $field))
                                                                    <span
                                                                        class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                                        <i class="bi bi-check-circle-fill small"></i>
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                                        <i class="bi bi-x-circle-fill small"></i>
                                                                    </span>
                                                                @endif
                                                            @break

                                                            @case('badge_image')
                                                                @if (data_get($item, $field))
                                                                    <span
                                                                        class="badge bg-info-subtle text-info border border-info-subtle">
                                                                        <i class="bi bi-image small me-1"></i>Sí
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                                        <i class="bi bi-image small me-1"></i>No
                                                                    </span>
                                                                @endif
                                                            @break

                                                            @case('badge_status')
                                                                @if (data_get($item, $field) == 'active')
                                                                    <span
                                                                        class="badge bg-success-subtle text-success border border-success-subtle">
                                                                        <i class="bi bi-circle-fill small me-1"></i>Activo
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                                        <i class="bi bi-circle-fill small me-1"></i>Inactivo
                                                                    </span>
                                                                @endif
                                                            @break

                                                            @case('stock')
                                                                @php
                                                                    $total_available = $item->inventory->sum(
                                                                        'quantity_available',
                                                                    );
                                                                    $total_reserved = $item->inventory->sum(
                                                                        'quantity_reserved',
                                                                    );
                                                                    $net_available = $total_available - $total_reserved;

                                                                    $stockClass = 'text-success';
                                                                    if (
                                                                        $net_available <= $item->stock_minimo &&
                                                                        $item->stock_minimo > 0
                                                                    ) {
                                                                        $stockClass = 'text-danger';
                                                                    } elseif (
                                                                        $net_available <= $item->stock_minimo * 1.5 &&
                                                                        $item->stock_minimo > 0
                                                                    ) {
                                                                        $stockClass = 'text-warning';
                                                                    }
                                                                @endphp
                                                                <span
                                                                    class="fw-bold {{ $stockClass }} small">{{ $net_available }}</span>
                                                            @break

                                                            @case('relation')
                                                                <span class="text-muted small">
                                                                    {{ data_get($item, $column['relation']) ?? '-' }}
                                                                </span>
                                                            @break

                                                            @case('image')
                                                                @php
                                                                    $imageUrl = data_get($item, $field);
                                                                    $width = $column['image_width'] ?? 40;
                                                                    $height = $column['image_height'] ?? 40;
                                                                    $borderRadius =
                                                                        $column['image_rounded'] ?? 'rounded';
                                                                    $defaultIcon =
                                                                        $column['default_icon'] ?? 'bi-image';
                                                                    $alt =
                                                                        $column['image_alt'] ??
                                                                        ($item->name ?? ($item->nombre ?? 'Imagen'));
                                                                @endphp
                                                                @if ($imageUrl)
                                                                    <img src="{{ $imageUrl }}" alt="{{ $alt }}"
                                                                        class="{{ $borderRadius }}"
                                                                        width="{{ $width }}"
                                                                        height="{{ $height }}"
                                                                        style="object-fit: cover;">
                                                                @else
                                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                        style="width: {{ $width }}px; height: {{ $height }}px;">
                                                                        <i class="bi {{ $defaultIcon }} text-muted"></i>
                                                                    </div>
                                                                @endif
                                                            @break

                                                            @case('image_circle')
                                                                @php
                                                                    $imageUrl = data_get($item, $field);
                                                                    $size = $column['image_size'] ?? 40;
                                                                    $defaultIcon =
                                                                        $column['default_icon'] ?? 'bi-person';
                                                                    $alt =
                                                                        $column['image_alt'] ??
                                                                        ($item->name ?? ($item->nombre ?? 'Imagen'));
                                                                @endphp
                                                                @if ($imageUrl)
                                                                    <img src="{{ $imageUrl }}" alt="{{ $alt }}"
                                                                        class="rounded-circle" width="{{ $size }}"
                                                                        height="{{ $size }}"
                                                                        style="object-fit: cover;">
                                                                @else
                                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                                                        style="width: {{ $size }}px; height: {{ $size }}px;">
                                                                        <i class="bi {{ $defaultIcon }} text-muted"></i>
                                                                    </div>
                                                                @endif
                                                            @break

                                                            @case('image_thumbnail')
                                                                @php
                                                                    $imageUrl = data_get($item, $field);
                                                                    $width = $column['image_width'] ?? 50;
                                                                    $height = $column['image_height'] ?? 50;
                                                                    $defaultIcon =
                                                                        $column['default_icon'] ?? 'bi-file-image';
                                                                    $alt =
                                                                        $column['image_alt'] ??
                                                                        ($item->name ?? ($item->nombre ?? 'Miniatura'));
                                                                @endphp
                                                                @if ($imageUrl)
                                                                    <a href="{{ $imageUrl }}" target="_blank"
                                                                        class="d-inline-block">
                                                                        <img src="{{ $imageUrl }}"
                                                                            alt="{{ $alt }}" class="img-thumbnail"
                                                                            width="{{ $width }}"
                                                                            height="{{ $height }}"
                                                                            style="object-fit: cover;">
                                                                    </a>
                                                                @else
                                                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                                                        style="width: {{ $width }}px; height: {{ $height }}px;">
                                                                        <i
                                                                            class="bi {{ $defaultIcon }} text-muted small"></i>
                                                                    </div>
                                                                @endif
                                                            @break

                                                            @default
                                                                {{ data_get($item, $field) ?: '-' }}
                                                        @endswitch
                                                    @elseif(isset($column['callback']))
                                                        {!! $column['callback']($item) !!}
                                                    @else
                                                        {{ data_get($item, $field) ?: '-' }}
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination Footer -->
                    @if ($isPaginated)
                        <div class="card-footer bg-white border-top py-3">
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div class="text-muted small">
                                    Mostrando <strong>{{ $firstItem }}-{{ $lastItem }}</strong>
                                    de <strong>{{ $total }}</strong> registros
                                </div>

                                <div>
                                    {{ $items->appends(request()->except('page'))->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-box display-1 text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">{{ $emptyMessage }}</h5>
                        @if ($createRoute)
                            <p class="text-muted mb-4">Comienza agregando un nuevo registro</p>
                            <a href="{{ $createRoute }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Agregar
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Estilos para el sistema de columnas y filtros */
        .columns-sidebar,
        .filters-sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            width: 380px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
            z-index: 1080;
            transition: right 0.3s ease;
            border-left: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
        }

        .columns-sidebar.show,
        .filters-sidebar.show {
            right: 0;
        }

        .columns-sidebar .sidebar-header,
        .filters-sidebar .sidebar-header {
            background: #1a4675;
            border-bottom: 1px solid #dee2e6;
            padding: 20px;
            position: relative;
        }

        .filters-sidebar .sidebar-header {
            background: #28a745;
        }

        .columns-sidebar .sidebar-header h5,
        .filters-sidebar .sidebar-header h5 {
            color: white;
            margin: 0;
            font-weight: 600;
        }

        .close-sidebar {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            position: absolute;
            right: 15px;
            top: 15px;
            cursor: pointer;
        }

        .columns-sidebar .sidebar-body,
        .filters-sidebar .sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .columns-sidebar .sidebar-footer,
        .filters-sidebar .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #dee2e6;
            background: #f8f9fa;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1070;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }

        .column-item-simple {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            transition: all 0.2s;
            margin-bottom: 8px;
        }

        .column-item-simple:hover {
            background: #f8f9fa;
            border-color: #28a745;
        }

        .column-checkbox-simple {
            margin-right: 15px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .column-label {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .column-display-name {
            font-size: 14px;
            font-weight: 500;
            color: #212529;
        }

        .bulk-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .bulk-actions .btn {
            flex: 1;
            padding: 8px 12px;
            font-size: 13px;
        }

        /* Drag & Drop */
        th[draggable="true"] {
            cursor: move;
            position: relative;
            user-select: none;
        }

        th[draggable="true"]:hover::after {
            content: '↕';
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 12px;
        }

        .dragging-column {
            opacity: 0.5;
            background: rgba(40, 167, 69, 0.1);
        }

        .drop-indicator {
            position: absolute;
            background: #28a745;
            width: 2px;
            height: 100%;
            top: 0;
            display: none;
            pointer-events: none;
            z-index: 100;
        }

        .drop-indicator.visible {
            display: block;
        }

        /* Filtros */
        .filter-section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: #f8f9fa;
        }

        .filter-section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a4675;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 4px;
            display: block;
        }

        .filter-category-title {
            font-size: 16px;
            font-weight: 600;
            color: #28a745;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #28a745;
        }

        /* Badge de filtros activos */
        #activeFiltersBadge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 10px;
            padding: 2px 5px;
        }

        /* Notificaciones */
        .alert-cufeso {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1090;
            min-width: 300px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {

            .columns-sidebar,
            .filters-sidebar {
                width: 100%;
                right: -100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script>
        // Overlay Manager
        class OverlayManager {
            constructor() {
                this.overlay = null;
                this.activeSidebars = new Set();
            }

            getOverlay() {
                if (!this.overlay) {
                    this.overlay = document.createElement('div');
                    this.overlay.className = 'sidebar-overlay';
                    this.overlay.onclick = () => this.hideAllSidebars();
                    document.body.appendChild(this.overlay);
                }
                return this.overlay;
            }

            showOverlay() {
                const overlay = this.getOverlay();
                overlay.classList.add('show');
            }

            hideOverlay() {
                if (this.overlay && this.activeSidebars.size === 0) {
                    this.overlay.classList.remove('show');
                    setTimeout(() => {
                        if (this.activeSidebars.size === 0 && this.overlay) {
                            this.overlay.remove();
                            this.overlay = null;
                        }
                    }, 300);
                }
            }

            registerSidebar(sidebarId) {
                this.activeSidebars.add(sidebarId);
                this.showOverlay();
            }

            unregisterSidebar(sidebarId) {
                this.activeSidebars.delete(sidebarId);
                this.hideOverlay();
            }

            hideAllSidebars() {
                document.querySelectorAll('.columns-sidebar.show, .filters-sidebar.show').forEach(sidebar => {
                    sidebar.classList.remove('show');
                });
                this.activeSidebars.clear();
                this.hideOverlay();
            }
        }

        // Table Manager
        class TableManager {
            constructor(tableId, config = {}) {
                this.tableId = tableId;
                this.table = document.getElementById(tableId);
                this.config = {
                    storagePrefix: 'table_',
                    enableColumns: true,
                    enableDragAndDrop: {{ $enableDragAndDrop ? 'true' : 'false' }},
                    ...config
                };

                this.columns = [];
                this.columnOrder = [];
                this.columnVisibility = {};
                this.filters = {};
                this.filterPresets = {};

                this.init();
            }

            init() {
                this.loadConfig();
                this.initColumns();
                if (this.config.enableDragAndDrop) {
                    this.initDragAndDrop();
                }
                this.setupEventListeners();
                this.updateFiltersBadge();
            }

            loadConfig() {
                const savedConfig = localStorage.getItem(`${this.config.storagePrefix}${this.tableId}_config`);
                if (savedConfig) {
                    const config = JSON.parse(savedConfig);
                    this.columnOrder = config.columnOrder || [];
                    this.columnVisibility = config.columnVisibility || {};

                    // Asegurar que actions SIEMPRE esté al inicio
                    if (this.columnOrder.includes('actions')) {
                        this.columnOrder = this.columnOrder.filter(field => field !== 'actions');
                    }
                    this.columnOrder.unshift('actions');
                    this.columnVisibility['actions'] = true;
                }

                // Cargar filtros guardados
                const savedFilters = localStorage.getItem(`${this.config.storagePrefix}${this.tableId}_filters`);
                if (savedFilters) {
                    this.filters = JSON.parse(savedFilters);
                }

                // Cargar presets de filtros
                const savedPresets = localStorage.getItem(`${this.config.storagePrefix}${this.tableId}_presets`);
                if (savedPresets) {
                    this.filterPresets = JSON.parse(savedPresets);
                }
            }

            saveConfig() {
                const config = {
                    columnOrder: this.columnOrder,
                    columnVisibility: this.columnVisibility
                };
                localStorage.setItem(`${this.config.storagePrefix}${this.tableId}_config`, JSON.stringify(config));
            }

            saveFilters() {
                localStorage.setItem(`${this.config.storagePrefix}${this.tableId}_filters`, JSON.stringify(this
                    .filters));
            }

            initColumns() {
                const thead = this.table.querySelector('thead tr');
                if (!thead) return;

                this.columns = Array.from(thead.querySelectorAll('th')).map((th, index) => {
                    const dbField = th.dataset.dbField || th.textContent.trim().toLowerCase().replace(/\s+/g,
                        '_');
                    const displayName = th.textContent.trim();

                    let isVisible;
                    if (dbField === 'actions') {
                        isVisible = true;
                    } else {
                        isVisible = this.columnVisibility[dbField] !== false;
                    }

                    return {
                        index,
                        dbField,
                        displayName,
                        element: th,
                        visible: isVisible,
                        isActions: dbField === 'actions'
                    };
                });

                // Aplicar orden guardado
                if (this.columnOrder.length > 0) {
                    this.applyColumnOrder();
                }

                // Aplicar visibilidad
                this.applyColumnVisibility();
            }

            initDragAndDrop() {
                const thead = this.table.querySelector('thead tr');
                if (!thead) return;

                const headers = thead.querySelectorAll('th');
                const tableWrapper = this.table.closest('.table-wrapper') || this.table.parentElement;

                // Crear indicador de drop
                const dropIndicator = document.createElement('div');
                dropIndicator.className = 'drop-indicator';
                tableWrapper.appendChild(dropIndicator);

                headers.forEach(header => {
                    const dbField = header.dataset.dbField;

                    if (dbField === 'actions') {
                        header.removeAttribute('draggable');
                        return;
                    }

                    header.setAttribute('draggable', 'true');

                    header.addEventListener('dragstart', (e) => {
                        e.dataTransfer.setData('text/plain', dbField);
                        header.classList.add('dragging-column');
                        e.dataTransfer.effectAllowed = 'move';
                    });

                    header.addEventListener('dragend', () => {
                        header.classList.remove('dragging-column');
                        dropIndicator.classList.remove('visible');
                        this.saveColumnOrder();
                    });
                });

                thead.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';

                    const draggingHeader = document.querySelector('.dragging-column');
                    if (!draggingHeader) return;

                    const draggableHeaders = Array.from(thead.querySelectorAll('th[draggable="true"]'));
                    const afterElement = this.getDragAfterElement(thead, e.clientX, draggableHeaders);

                    if (afterElement) {
                        const rect = afterElement.getBoundingClientRect();
                        const tableRect = tableWrapper.getBoundingClientRect();

                        dropIndicator.style.left = `${rect.left - tableRect.left - 1}px`;
                        dropIndicator.style.top = `${rect.top - tableRect.top}px`;
                        dropIndicator.style.height = `${rect.height}px`;
                        dropIndicator.classList.add('visible');
                    } else {
                        const lastDraggableHeader = draggableHeaders[draggableHeaders.length - 1];
                        if (lastDraggableHeader) {
                            const rect = lastDraggableHeader.getBoundingClientRect();
                            const tableRect = tableWrapper.getBoundingClientRect();

                            dropIndicator.style.left = `${rect.right - tableRect.left - 1}px`;
                            dropIndicator.style.top = `${rect.top - tableRect.top}px`;
                            dropIndicator.style.height = `${rect.height}px`;
                            dropIndicator.classList.add('visible');
                        }
                    }
                });

                thead.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropIndicator.classList.remove('visible');

                    const field = e.dataTransfer.getData('text/plain');
                    if (!field) return;

                    const draggingHeader = document.querySelector('.dragging-column');
                    if (!draggingHeader) return;

                    const allHeaders = Array.from(thead.querySelectorAll('th'));
                    const draggableHeaders = allHeaders.filter(h => h.dataset.dbField !== 'actions');

                    const afterElement = this.getDragAfterElement(thead, e.clientX, draggableHeaders);

                    if (afterElement) {
                        thead.insertBefore(draggingHeader, afterElement);
                    } else {
                        const actionsHeader = allHeaders.find(h => h.dataset.dbField === 'actions');
                        if (actionsHeader) {
                            thead.insertBefore(draggingHeader, actionsHeader.nextSibling);
                        } else {
                            thead.appendChild(draggingHeader);
                        }
                    }

                    this.moveColumnCells(field);
                    this.updateColumnOrder();
                });
            }

            getDragAfterElement(container, x, draggableElements) {
                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = x - box.left - box.width / 2;

                    if (offset < 0 && offset > closest.offset) {
                        return {
                            offset: offset,
                            element: child
                        };
                    } else {
                        return closest;
                    }
                }, {
                    offset: Number.NEGATIVE_INFINITY
                }).element;
            }

            moveColumnCells(field) {
                const tbody = this.table.querySelector('tbody');
                if (!tbody) return;

                const rows = tbody.querySelectorAll('tr');
                const thead = this.table.querySelector('thead tr');
                const headers = Array.from(thead.querySelectorAll('th'));

                const headerOrder = headers.map(h => h.dataset.dbField).filter(f => f);
                const newIndex = headerOrder.indexOf(field);

                rows.forEach(row => {
                    const cells = Array.from(row.children);
                    let cellToMove = null;

                    for (let i = 0; i < cells.length; i++) {
                        if (cells[i].dataset.field === field) {
                            cellToMove = cells[i];
                            break;
                        }
                    }

                    if (cellToMove) {
                        row.removeChild(cellToMove);

                        if (newIndex >= cells.length) {
                            row.appendChild(cellToMove);
                        } else {
                            const referenceCell = row.children[newIndex];
                            if (referenceCell) {
                                row.insertBefore(cellToMove, referenceCell);
                            } else {
                                row.appendChild(cellToMove);
                            }
                        }
                    }
                });
            }

            updateColumnOrder() {
                const thead = this.table.querySelector('thead tr');
                const headers = Array.from(thead.querySelectorAll('th'));

                this.columnOrder = headers
                    .filter(header => header.dataset.dbField && header.dataset.dbField !== 'actions')
                    .map(header => header.dataset.dbField);

                this.saveConfig();
            }

            saveColumnOrder() {
                this.updateColumnOrder();
                this.saveConfig();
            }

            applyColumnOrder() {
                const thead = this.table.querySelector('thead tr');
                const headers = Array.from(thead.querySelectorAll('th'));

                const actionsHeader = headers.find(h => h.dataset.dbField === 'actions');
                const otherHeaders = headers.filter(h => h.dataset.dbField !== 'actions');

                const headerMap = {};
                otherHeaders.forEach(header => {
                    headerMap[header.dataset.dbField] = header;
                });

                thead.innerHTML = '';

                if (actionsHeader) {
                    thead.appendChild(actionsHeader);
                }

                const otherFields = this.columnOrder.filter(field => field !== 'actions');
                otherFields.forEach(field => {
                    const header = headerMap[field];
                    if (header) {
                        thead.appendChild(header);
                    }
                });

                otherHeaders.forEach(header => {
                    const field = header.dataset.dbField;
                    if (!otherFields.includes(field)) {
                        thead.appendChild(header);
                    }
                });

                this.reorderBodyCells();
            }

            reorderBodyCells() {
                const tbody = this.table.querySelector('tbody');
                if (!tbody) return;

                const rows = tbody.querySelectorAll('tr');
                const headers = Array.from(this.table.querySelectorAll('thead th'));
                const realOrder = headers.map(h => h.dataset.dbField).filter(f => f);

                rows.forEach(row => {
                    const cells = Array.from(row.children);
                    const cellMap = {};

                    cells.forEach(cell => {
                        const field = cell.dataset.field;
                        if (field) {
                            cellMap[field] = cell;
                        }
                    });

                    while (row.firstChild) {
                        row.removeChild(row.firstChild);
                    }

                    realOrder.forEach(field => {
                        const cell = cellMap[field];
                        if (cell) {
                            row.appendChild(cell);
                        }
                    });
                });
            }

            applyColumnVisibility() {
                const thead = this.table.querySelector('thead tr');
                const tbody = this.table.querySelector('tbody');

                this.columns.forEach(column => {
                    const header = thead.querySelector(`th[data-db-field="${column.dbField}"]`);
                    const cells = tbody.querySelectorAll(`td[data-field="${column.dbField}"]`);

                    if (column.dbField === 'actions') {
                        if (header) header.style.display = '';
                        cells.forEach(cell => cell.style.display = '');
                        return;
                    }

                    if (column.visible) {
                        if (header) header.style.display = '';
                        cells.forEach(cell => cell.style.display = '');
                    } else {
                        if (header) header.style.display = 'none';
                        cells.forEach(cell => cell.style.display = 'none');
                    }
                });
            }

            showColumnsSidebar() {
                const sidebarId = `${this.tableId}-columns-sidebar`;

                if (document.getElementById(sidebarId)) {
                    return;
                }

                window.overlayManager.registerSidebar(sidebarId);

                const sidebar = document.createElement('div');
                sidebar.className = 'columns-sidebar';
                sidebar.id = sidebarId;
                sidebar.innerHTML = `
            <div class="sidebar-header">
                <h5><i class="bi bi-columns-gap me-2"></i> Mostrar/Ocultar Columnas</h5>
                <button class="close-sidebar" onclick="tableManager.hideColumnsSidebar()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="sidebar-body">
                <div class="bulk-actions">
                    <button class="btn btn-outline-primary btn-sm" onclick="tableManager.showAllColumns()">
                        <i class="bi bi-eye me-1"></i> Mostrar todas
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="tableManager.hideAllColumns()">
                        <i class="bi bi-eye-slash me-1"></i> Ocultar todas
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="tableManager.resetColumns()"
                            title="Restablecer orden y visibilidad original">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
                <div class="columns-list">
                    ${this.createColumnsList()}
                </div>
            </div>
        `;

                document.body.appendChild(sidebar);

                setTimeout(() => {
                    sidebar.classList.add('show');
                }, 10);
            }

            createColumnsList() {
                let content = '';

                this.columns.forEach(column => {
                    if (column.dbField !== 'actions') {
                        content += `
                    <div class="column-item-simple">
                        <input type="checkbox"
                            class="column-checkbox-simple"
                            id="col-${column.dbField}"
                            ${column.visible ? 'checked' : ''}
                            onchange="tableManager.toggleColumn('${column.dbField}', this.checked)">
                        <label class="column-label" for="col-${column.dbField}">
                            <span class="column-display-name">${column.displayName}</span>
                        </label>
                    </div>
                `;
                    }
                });

                return content;
            }

            hideColumnsSidebar() {
                const sidebar = document.getElementById(`${this.tableId}-columns-sidebar`);
                if (sidebar) {
                    sidebar.classList.remove('show');
                    window.overlayManager.unregisterSidebar(`${this.tableId}-columns-sidebar`);
                    setTimeout(() => {
                        if (sidebar.parentNode) {
                            sidebar.parentNode.removeChild(sidebar);
                        }
                    }, 300);
                }
            }

            toggleColumn(field, visible) {
                const column = this.columns.find(col => col.dbField === field);
                if (column) {
                    column.visible = visible;
                    this.columnVisibility[field] = visible;
                    this.applyColumnVisibilityForField(field, visible);
                    this.saveConfig();
                }
            }

            applyColumnVisibilityForField(field, visible) {
                const thead = this.table.querySelector('thead tr');
                const tbody = this.table.querySelector('tbody');

                const header = thead.querySelector(`th[data-db-field="${field}"]`);
                const cells = tbody.querySelectorAll(`td[data-field="${field}"]`);

                if (header) header.style.display = visible ? '' : 'none';
                cells.forEach(cell => cell.style.display = visible ? '' : 'none');
            }

            showAllColumns() {
                this.columns.forEach(column => {
                    if (column.dbField !== 'actions') {
                        this.toggleColumn(column.dbField, true);
                        const checkbox = document.getElementById(`col-${column.dbField}`);
                        if (checkbox) checkbox.checked = true;
                    }
                });
            }

            hideAllColumns() {
                this.columns.forEach(column => {
                    if (column.dbField !== 'actions') {
                        this.toggleColumn(column.dbField, false);
                        const checkbox = document.getElementById(`col-${column.dbField}`);
                        if (checkbox) checkbox.checked = false;
                    }
                });
            }

            resetColumns() {
                localStorage.removeItem(`${this.config.storagePrefix}${this.tableId}_config`);
                location.reload();
            }

            showFiltersSidebar() {
                const sidebarId = `${this.tableId}-filters-sidebar`;

                if (document.getElementById(sidebarId)) {
                    return;
                }

                window.overlayManager.registerSidebar(sidebarId);

                const filterConfig = @json($filters);

                const sidebar = document.createElement('div');
                sidebar.className = 'filters-sidebar';
                sidebar.id = sidebarId;
                sidebar.innerHTML = `
            <div class="sidebar-header">
                <h5><i class="bi bi-funnel me-2"></i> Filtros Avanzados</h5>
                <button class="close-sidebar" onclick="tableManager.hideFiltersSidebar()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="sidebar-body">
                <div class="mb-3">
                    <input type="text" class="form-control form-control-sm" id="filter-search" placeholder="Buscar filtro...">
                </div>
                ${this.createFiltersContent(filterConfig)}
            </div>
            <div class="sidebar-footer">
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary flex-fill" onclick="tableManager.clearFilters()">
                        <i class="bi bi-x-circle me-1"></i> Limpiar
                    </button>
                    <button class="btn btn-primary flex-fill" onclick="tableManager.applyFilters()">
                        <i class="bi bi-check-circle me-1"></i> Aplicar
                    </button>
                    <button class="btn btn-success flex-fill" onclick="tableManager.savePreset()">
                        <i class="bi bi-save me-1"></i> Guardar
                    </button>
                </div>
                ${this.createPresetsDropdown()}
            </div>
        `;

                document.body.appendChild(sidebar);

                setTimeout(() => {
                    sidebar.classList.add('show');
                    this.initFilterSearch();
                    this.populateFilterInputs();
                }, 10);
            }

            createFiltersContent(filterConfig) {
                let content = '';

                if (filterConfig && filterConfig.length > 0) {
                    filterConfig.forEach(field => {
                        content += this.createFilterField(field);
                    });
                }

                return content;
            }

            createFilterField(field) {
                const fieldId = `filter-${field.name}`;
                let fieldHtml = '';

                switch (field.type) {
                    case 'select':
                        fieldHtml = `
                    <select class="form-select form-select-sm" id="${fieldId}" data-filter="${field.name}">
                        <option value="">${field.placeholder || 'Todos'}</option>
                        ${Object.entries(field.options).map(([value, label]) =>
                            `<option value="${value}">${label}</option>`
                        ).join('')}
                    </select>
                `;
                        break;

                    case 'daterange':
                        fieldHtml = `
                    <div class="d-flex gap-2">
                        <input type="date" class="form-control form-control-sm" id="${fieldId}_from" data-filter="${field.name}_from" placeholder="Desde">
                        <span class="text-muted">a</span>
                        <input type="date" class="form-control form-control-sm" id="${fieldId}_to" data-filter="${field.name}_to" placeholder="Hasta">
                    </div>
                `;
                        break;

                    case 'text':
                    default:
                        fieldHtml = `
                    <input type="text" class="form-control form-control-sm" id="${fieldId}" data-filter="${field.name}" placeholder="${field.placeholder || 'Buscar...'}">
                `;
                }

                return `
            <div class="filter-section" data-filter-name="${field.name}">
                <div class="filter-section-title">${field.label}</div>
                ${fieldHtml}
            </div>
        `;
            }

            initFilterSearch() {
                const searchInput = document.getElementById('filter-search');
                if (searchInput) {
                    searchInput.addEventListener('input', (e) => {
                        const searchTerm = e.target.value.toLowerCase();
                        const filterSections = document.querySelectorAll('.filter-section');

                        filterSections.forEach(section => {
                            const title = section.querySelector('.filter-section-title').textContent
                                .toLowerCase();
                            section.style.display = title.includes(searchTerm) ? 'block' : 'none';
                        });
                    });
                }
            }

            populateFilterInputs() {
                Object.entries(this.filters).forEach(([field, value]) => {
                    const input = document.getElementById(`filter-${field}`);
                    if (input) {
                        input.value = value;
                    }
                });
            }

            hideFiltersSidebar() {
                const sidebar = document.getElementById(`${this.tableId}-filters-sidebar`);
                if (sidebar) {
                    sidebar.classList.remove('show');
                    window.overlayManager.unregisterSidebar(`${this.tableId}-filters-sidebar`);
                    setTimeout(() => {
                        if (sidebar.parentNode) {
                            sidebar.parentNode.removeChild(sidebar);
                        }
                    }, 300);
                }
            }

            applyFilters() {
                this.filters = {};
                const filterInputs = document.querySelectorAll(`#${this.tableId}-filters-sidebar [data-filter]`);

                filterInputs.forEach(input => {
                    const field = input.dataset.filter;
                    const value = input.value.trim();

                    if (value) {
                        this.filters[field] = value;
                    }
                });

                this.saveFilters();
                this.applyFiltersToTable();
                this.hideFiltersSidebar();
                this.updateFiltersBadge();
                this.showNotification(`Filtros aplicados (${Object.keys(this.filters).length} activos)`, 'success');
            }

            applyFiltersToTable() {
                const tbody = this.table.querySelector('tbody');
                if (!tbody) return;

                const rows = tbody.querySelectorAll('tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    let showRow = true;

                    Object.entries(this.filters).forEach(([field, value]) => {
                        if (!showRow) return;

                        const cell = row.querySelector(`[data-field="${field}"]`);
                        if (cell) {
                            const cellText = cell.textContent.toLowerCase();
                            if (!cellText.includes(value.toLowerCase())) {
                                showRow = false;
                            }
                        }
                    });

                    row.style.display = showRow ? '' : 'none';
                    if (showRow) visibleCount++;
                });

                this.updateRowCount(visibleCount);
            }

            clearFilters() {
                const filterInputs = document.querySelectorAll(`#${this.tableId}-filters-sidebar [data-filter]`);
                filterInputs.forEach(input => {
                    input.value = '';
                });

                this.filters = {};
                this.saveFilters();

                const tbody = this.table.querySelector('tbody');
                if (tbody) {
                    const rows = tbody.querySelectorAll('tr');
                    rows.forEach(row => {
                        row.style.display = '';
                    });
                    this.updateRowCount(rows.length);
                }

                this.updateFiltersBadge();
                this.showNotification('Filtros limpiados', 'info');
            }

            savePreset() {
                const presetName = prompt('Nombre del preset de filtros:');
                if (presetName && presetName.trim()) {
                    this.filterPresets[presetName] = {
                        ...this.filters
                    };
                    localStorage.setItem(
                        `${this.config.storagePrefix}${this.tableId}_presets`,
                        JSON.stringify(this.filterPresets)
                    );
                    this.showNotification(`Preset "${presetName}" guardado`, 'success');
                }
            }

            createPresetsDropdown() {
                if (Object.keys(this.filterPresets).length === 0) {
                    return '';
                }

                let options = '<option value="">Presets guardados</option>';
                Object.keys(this.filterPresets).forEach(presetName => {
                    options += `<option value="${presetName}">${presetName}</option>`;
                });

                return `
            <div class="mt-3">
                <select class="form-select form-select-sm" id="filterPresets" onchange="tableManager.loadPreset(this.value)">
                    ${options}
                </select>
            </div>
        `;
            }

            loadPreset(presetName) {
                if (!presetName || !this.filterPresets[presetName]) return;

                this.filters = {
                    ...this.filterPresets[presetName]
                };
                this.applyFiltersToTable();
                this.saveFilters();
                this.updateFiltersBadge();
                this.showNotification(`Preset "${presetName}" cargado`, 'success');
            }

            updateRowCount(count) {
                const totalRows = this.table.querySelectorAll('tbody tr').length;
                const counter = document.getElementById('rowCounter');
                if (counter) {
                    if (Object.keys(this.filters).length > 0) {
                        counter.textContent = `${count} de ${totalRows} registros (filtrados)`;
                    } else {
                        counter.textContent = `${totalRows} registros totales`;
                    }
                }
            }

            updateFiltersBadge() {
                const badge = document.getElementById('activeFiltersBadge');
                if (badge) {
                    const activeCount = Object.keys(this.filters).length;
                    if (activeCount > 0) {
                        badge.textContent = activeCount;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                }
            }

            exportToExcel(route) {
                const exportBtn = event.target.closest('button');
                const originalHTML = exportBtn.innerHTML;
                exportBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i>';
                exportBtn.disabled = true;

                const urlParams = new URLSearchParams(window.location.search);
                const filters = {};

                for (const [key, value] of urlParams) {
                    if (key !== 'page') {
                        filters[key] = value;
                    }
                }

                const tableConfig = localStorage.getItem(`${this.config.storagePrefix}${this.tableId}_config`);
                const columnConfig = tableConfig ? JSON.parse(tableConfig) : {
                    columnVisibility: {},
                    columnOrder: []
                };

                const form = document.createElement('form');
                form.method = 'GET';
                form.action = route;
                form.style.display = 'none';

                Object.entries(filters).forEach(([key, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                });

                const columnInput = document.createElement('input');
                columnInput.type = 'hidden';
                columnInput.name = 'column_config';
                columnInput.value = JSON.stringify(columnConfig);
                form.appendChild(columnInput);

                document.body.appendChild(form);
                form.submit();

                setTimeout(() => {
                    if (form.parentNode) {
                        form.parentNode.removeChild(form);
                    }
                    exportBtn.innerHTML = originalHTML;
                    exportBtn.disabled = false;
                }, 2000);
            }

            showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type} alert-dismissible fade show alert-cufeso`;
                notification.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 3000);
            }

            setupEventListeners() {
                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const name = this.dataset.name || 'este registro';
                        if (confirm(`¿Estás seguro de eliminar ${name}?`)) {
                            this.closest('form').submit();
                        }
                    });
                });
            }
        }

        // Inicialización
        window.overlayManager = new OverlayManager();
        let tableManager;

        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('{{ $tableId }}');
            if (table) {
                tableManager = new TableManager('{{ $tableId }}', {
                    storagePrefix: 'cufeso_',
                    enableDragAndDrop: {{ $enableDragAndDrop ? 'true' : 'false' }}
                });
            }
        });

        window.tableManager = tableManager;
    </script>

    <style>
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
