@extends('layouts.demo')

@section('title', 'Demo - Sistema de Cotizaciones')

@section('content')
    <div class="px-6 py-6 space-y-6">

        @php
            $cotizacionesData = [];

            $clientes = [
                'JCB Maquinarias',
                'Gavsa Industrial',
                'JGV Construcciones',
                'Constructora ABC',
                'Logística Express',
                'Tech Solutions',
                'Minera del Norte',
                'Agroindustrias López',
                'Transportes Unidos',
                'Inmobiliaria Central',
                'Hotelera del Pacífico',
                'Ferretería El Constructor',
                'Taller Mecánico Rápido',
                'Distribuidora de Alimentos',
                'Textiles Modernos',
                'Plásticos Industriales',
                'Laboratorios Farmacéuticos',
                'Autopartes México',
                'Papelería y Más',
                'Equipos de Cómputo',
                'Muebles de Oficina',
                'Restaurante El Gourmet',
                'Gimnasio Fitness Center',
                'Clínica Dental',
                'Escuela de Idiomas',
                'Agencia de Viajes',
                'Publicidad Creativa',
                'Estudio de Diseño',
                'Consultoría Empresarial',
                'Seguros y Finanzas',
            ];

            $vendedores = [
                'Carlos Rodríguez',
                'María Pérez',
                'Juan Vásquez',
                'Ana González',
                'Luis Martínez',
                'Sofia Ramírez',
            ];

            $estados = ['aprobada', 'pendiente', 'rechazada', 'borrador', 'enviada'];

            $fechaInicio = strtotime('-3 months');
            $fechaFin = time();

            for ($i = 1; $i <= 30; $i++) {
                $codigo = 'COT-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                $cliente = $clientes[array_rand($clientes)];

                $fechaTimestamp = rand($fechaInicio, $fechaFin);
                $fecha = date('Y-m-d', $fechaTimestamp);

                $monto = rand(1000, 150000) + rand(0, 99) / 100;
                $items = rand(1, 15);

                $estado = $estados[array_rand($estados)];
                $vendedor = $vendedores[array_rand($vendedores)];

                $cotizacionesData[] = (object) [
                    'id' => $i,
                    'codigo' => $codigo,
                    'cliente' => $cliente,
                    'fecha' => $fecha,
                    'monto' => $monto,
                    'estado' => $estado,
                    'items' => $items,
                    'vendedor' => $vendedor,
                ];
            }

            usort($cotizacionesData, function ($a, $b) {
                $numA = intval(substr($a->codigo, -3));
                $numB = intval(substr($b->codigo, -3));
                return $numB - $numA;
            });

            $cotizaciones = collect($cotizacionesData);

            $totalCotizaciones = $cotizaciones->count();
            $aprobadas = $cotizaciones->where('estado', 'aprobada')->count();
            $pendientes = $cotizaciones->where('estado', 'pendiente')->count();
            $rechazadas = $cotizaciones->where('estado', 'rechazada')->count();
            $borradores = $cotizaciones->where('estado', 'borrador')->count();
            $enviadas = $cotizaciones->where('estado', 'enviada')->count();
            $montoTotal = $cotizaciones->sum('monto');

            $stats = [
                [
                    'label' => 'Total Cotizaciones',
                    'value' => $totalCotizaciones,
                    'icon' => 'fa-file-invoice',
                    'color' => 'text-blue-600',
                    'bg' => 'bg-blue-100',
                    'subtext' => 'registros',
                ],
                [
                    'label' => 'Aprobadas',
                    'value' => $aprobadas,
                    'icon' => 'fa-check-circle',
                    'color' => 'text-green-600',
                    'bg' => 'bg-green-100',
                    'subtext' => 'cotizaciones',
                    'percentage' => $totalCotizaciones > 0 ? round(($aprobadas / $totalCotizaciones) * 100) : 0,
                ],
                [
                    'label' => 'Pendientes',
                    'value' => $pendientes,
                    'icon' => 'fa-clock',
                    'color' => 'text-yellow-600',
                    'bg' => 'bg-yellow-100',
                    'subtext' => 'cotizaciones',
                    'percentage' => $totalCotizaciones > 0 ? round(($pendientes / $totalCotizaciones) * 100) : 0,
                ],
                [
                    'label' => 'Monto Total',
                    'value' => '$' . number_format($montoTotal, 2),
                    'icon' => 'fa-dollar-sign',
                    'color' => 'text-indigo-600',
                    'bg' => 'bg-indigo-100',
                    'subtext' => 'MXN',
                ],
            ];

            $columns = [
                ['field' => 'codigo', 'label' => 'Código', 'visible' => true, 'format' => 'code', 'align' => 'left'],
                ['field' => 'cliente', 'label' => 'Cliente', 'visible' => true, 'align' => 'left'],
                ['field' => 'fecha', 'label' => 'Fecha', 'visible' => true, 'align' => 'center', 'format' => 'date'],
                ['field' => 'monto', 'label' => 'Monto', 'format' => 'currency', 'align' => 'right', 'visible' => true],
                ['field' => 'estado', 'label' => 'Estado', 'visible' => true, 'align' => 'center', 'format' => 'badge'],
                ['field' => 'items', 'label' => 'Items', 'align' => 'center', 'visible' => true],
                ['field' => 'vendedor', 'label' => 'Vendedor', 'visible' => true, 'align' => 'left'],
            ];

            $actionButtons = '
<div class="relative">
<button class="px-2 py-1 text-gray-600 hover:text-black">
<i class="fas fa-ellipsis-v"></i>
</button>
</div>
';

        @endphp


        {{-- STATS --}}
        {{-- STATS --}}
        <x-demo.stats-cards :stats="$stats" />

        {{-- INFO --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg flex items-center gap-2">

            <i class="fas fa-info-circle"></i>

            <div>
                <strong>Mostrando 30 cotizaciones con información aleatoria</strong>
                <span class="text-sm text-blue-500">(COT-2025-030 a COT-2025-001)</span>
            </div>

        </div>

        <x-demo.table :items="$cotizaciones->map(fn($i) => (array) $i)->values()->all()" :columns="$columns" title="Listado de Cotizaciones"
            searchPlaceholder="Buscar por código, cliente o vendedor..." emptyMessage="No hay cotizaciones para mostrar"
            :itemsPerPage="10" />
    </div>
@endsection
