@props([
    'stats' => [],
    'columns' => 4,
])

@php
    // Mapeo de iconos (se mantiene igual para no romper nada)
    $iconMap = [
        'file-invoice' => 'request_quote',
        'check-circle' => 'check_circle',
        'clock' => 'schedule',
        'dollar-sign' => 'attach_money',
        'boxes' => 'inventory_2',
        'exchange-alt' => 'swap_horiz',
        'arrow-down' => 'south',
        'arrow-up' => 'north',
        'chart-line' => 'trending_up',
        'chart-bar' => 'bar_chart',
        'money-bill' => 'payments',
    ];

    // Tu mapa de colores original para las vistas que lo usan (con Dark Mode)
    $colorMap = [
        'primary' => [
            'bg' => 'bg-blue-100 dark:bg-blue-500/10',
            'icon' => 'text-blue-600 dark:text-blue-400',
            'bar' => 'bg-blue-600',
        ],
        'success' => [
            'bg' => 'bg-green-100 dark:bg-emerald-500/10',
            'icon' => 'text-green-600 dark:text-emerald-400',
            'bar' => 'bg-green-600',
        ],
        'warning' => [
            'bg' => 'bg-yellow-100 dark:bg-warning/20',
            'icon' => 'text-yellow-600 dark:text-warning',
            'bar' => 'bg-yellow-600',
        ],
        'danger' => [
            'bg' => 'bg-rose-100 dark:bg-danger/20',
            'icon' => 'text-rose-600 dark:text-danger',
            'bar' => 'bg-rose-600',
        ],
        'info' => [
            'bg' => 'bg-indigo-100 dark:bg-indigo-500/10',
            'icon' => 'text-indigo-600 dark:text-indigo-400',
            'bar' => 'bg-indigo-600',
        ],
    ];

    $gridCols = match ($columns) {
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4',
    };
@endphp

<div class="grid {{ $gridCols }} gap-6">
    @foreach ($stats as $stat)
        @php
            $colorKey = $stat['color'] ?? 'primary';

            // DETECCIÓN INTELIGENTE:
            // Si el color empieza con 'text-', es que viene de Cotizaciones (clase directa)
            if (str_starts_with($colorKey, 'text-')) {
                $iconClass = $colorKey;
                $bgClass = $stat['bg'] ?? 'bg-gray-100'; // Usa el bg que venga en el array
                $barClass = str_replace('text-', 'bg-', $colorKey); // Convierte text-blue a bg-blue
            } else {
                // Si no, usa tu mapa de colores original por nombre (primary, success...)
                $c = $colorMap[$colorKey] ?? $colorMap['primary'];
                $iconClass = $c['icon'];
                $bgClass = $c['bg'];
                $barClass = $c['bar'];
            }

            // Limpieza de icono
            $rawIcon = $stat['icon'] ?? 'chart-bar';
            $cleanIcon = str_replace(['fas ', 'fa-', 'fa '], '', $rawIcon);
            $icon = $iconMap[$cleanIcon] ?? 'bar_chart';
        @endphp

<div class="bg-white dark:bg-surface shadow-sm border border-gray-100 dark:border-white/[0.08] rounded-xl p-5 flex items-center gap-4 transition-all hover:shadow-md">

    {{-- ICONO --}}
    <div class="{{ $bgClass }} w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-lg">
        <span class="material-symbols-outlined {{ $iconClass }} text-[24px]" style="font-variation-settings:'FILL' 1">
            {{ $icon }}
        </span>
    </div>

    {{-- TEXTO --}}
    <div class="flex-1 min-w-0">
        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 truncate">
            {{ $stat['label'] }}
        </p>

        {{-- Valor Principal: Usamos break-all para que si es ENORME, salte de línea --}}
        <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight break-all">
            {{ $stat['value'] }}
        </h3>

        {{-- BARRA DE PROGRESO (Sin texto abajo) --}}
        @if(isset($stat['percentage']))
            <div class="mt-3 w-full bg-gray-200 dark:bg-white/10 rounded-full h-1 overflow-hidden">
                <div class="{{ $barClass }} h-1 rounded-full transition-all duration-700"
                     style="width: {{ $stat['percentage'] }}%"></div>
            </div>
        @endif
    </div>
</div>
    @endforeach
</div>
