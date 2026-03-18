{{-- file: resources/views/demos/inventario-existencias.blade.php --}}
@extends('layouts.demo')
@section('title', 'Movimientos de Inventario — Demo')

@section('content')

    <div class="px-6 py-6">

        @php

            $movData = [];

            $prods = [
                ['sku' => 'SKU-ABC-0001', 'nombre' => 'Taladro Percutor 800W Bosch'],
                ['sku' => 'SKU-DEF-0002', 'nombre' => 'Martillo Demoledor 1500W DeWalt'],
                ['sku' => 'SKU-GHI-0003', 'nombre' => 'Sierra Circular 1200W Makita'],
                ['sku' => 'SKU-JKL-0004', 'nombre' => 'Lijadora Orbital 300W B+D'],
                ['sku' => 'SKU-MNO-0005', 'nombre' => 'Compresor 25L Truper'],
                ['sku' => 'SKU-PQR-0006', 'nombre' => 'Soldador Inverter 160A Urrea'],
                ['sku' => 'SKU-STU-0007', 'nombre' => 'Hidrolavadora 1800W Kärcher'],
                ['sku' => 'SKU-VWX-0008', 'nombre' => 'Pulidora 750W Stanley'],
                ['sku' => 'SKU-YZA-0009', 'nombre' => 'Atornillador 12V Milwaukee'],
                ['sku' => 'SKU-BCD-0010', 'nombre' => 'Multímetro Digital Fluke'],
            ];

            $usuarios = ['Carlos Rodríguez', 'María Pérez', 'Juan Vásquez', 'Ana González', 'Luis Martínez'];

            $motivosE = [
                'Compra a proveedor',
                'Devolución de cliente',
                'Ajuste de inventario',
                'Transferencia',
                'Producción',
            ];

            $motivosS = ['Venta a cliente', 'Devolución a proveedor', 'Ajuste', 'Consumo interno', 'Merma'];

            $fi = strtotime('-30 days');
            $ff = time();

            for ($i = 1; $i <= 50; $i++) {
                $tipo = rand(0, 1) ? 'entrada' : 'salida';

                $prod = $prods[array_rand($prods)];

                $ft = rand($fi, $ff);

                $movData[] = (object) [
                    'id' => $i,
                    'folio' => 'MOV-' . date('Ymd', $ft) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'fecha' => date('Y-m-d H:i', $ft),
                    'tipo' => $tipo,
                    'producto_sku' => $prod['sku'],
                    'producto_nombre' => $prod['nombre'],
                    'cantidad' => $tipo == 'entrada' ? rand(5, 100) : rand(1, 30),
                    'motivo' =>
                        $tipo == 'entrada' ? $motivosE[array_rand($motivosE)] : $motivosS[array_rand($motivosS)],
                    'usuario' => $usuarios[array_rand($usuarios)],
                    'costo_unitario' => rand(100, 5000) + rand(0, 99) / 100,
                ];
            }

            usort($movData, fn($a, $b) => strtotime($b->fecha) - strtotime($a->fecha));

            $movimientos = collect($movData);

            $tTot = $movimientos->count();
            $tEnt = $movimientos->where('tipo', 'entrada')->count();
            $tSal = $movimientos->where('tipo', 'salida')->count();

            $uEnt = $movimientos->where('tipo', 'entrada')->sum('cantidad');
            $uSal = $movimientos->where('tipo', 'salida')->sum('cantidad');

            $vTot = $movimientos->sum(fn($m) => $m->cantidad * $m->costo_unitario);

            /* ========================
   STATS
======================== */

            $stats = [
                [
                    'label' => 'Total Movimientos',
                    'value' => $tTot,
                    'icon' => 'swap_horiz',
                    'color' => 'primary',
                    'subtext' => 'registros',
                ],

                [
                    'label' => 'Entradas',
                    'value' => $tEnt,
                    'icon' => 'south',
                    'color' => 'success',
                    'subtext' => number_format($uEnt) . ' unidades',
                ],

                [
                    'label' => 'Salidas',
                    'value' => $tSal,
                    'icon' => 'north',
                    'color' => 'danger',
                    'subtext' => number_format($uSal) . ' unidades',
                ],

                [
                    'label' => 'Valor Total',
                    'value' => '$' . number_format($vTot, 2),
                    'icon' => 'trending_up',
                    'color' => 'info',
                    'subtext' => 'movimientos',
                ],
            ];

            /* ========================
   COLUMNAS TABLA
======================== */

            $columns = [
                [
                    'field' => 'folio',
                    'label' => 'Folio',
                    'format' => 'code',
                ],

                [
                    'field' => 'fecha',
                    'label' => 'Fecha',
                ],

                [
                    'field' => 'tipo',
                    'label' => 'Tipo',
                    'format' => 'badge',
                ],

                [
                    'field' => 'producto_sku',
                    'label' => 'SKU',
                ],

                [
                    'field' => 'producto_nombre',
                    'label' => 'Producto',
                ],

                [
                    'field' => 'cantidad',
                    'label' => 'Cantidad',
                    'align' => 'center',
                ],

                [
                    'field' => 'motivo',
                    'label' => 'Motivo',
                ],

                [
                    'field' => 'usuario',
                    'label' => 'Usuario',
                ],
            ];

        @endphp



        <x-demo.stats-cards :stats="$stats" module="inventario" />


        <div
            class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-info/10 border border-info/20 text-info text-sm mt-6">

            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[17px]">today</span>
                <span>
                    <strong>Resumen del día —</strong>
                    {{ rand(0, 10) }} entradas | {{ rand(0, 8) }} salidas
                </span>
            </div>

            <button onclick="exportarReporte()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-info/20 text-info hover:bg-info/30 transition-all">

                <span class="material-symbols-outlined text-[14px]">download</span>
                Exportar

            </button>

        </div>


        <div class="mt-6">

            <x-demo.table :items="$movimientos" :columns="$columns" title="Historial de Movimientos"
                searchPlaceholder="Buscar movimiento..." module="inventario" :itemsPerPage="10" showPagination="true"
                exportable="true" />

        </div>


    </div>

@endsection


@push('scripts')
    <script>
        function exportarReporte() {

            Swal.fire({
                confirmButtonColor: '#2563eb',
                title: '📊 Exportando',
                text: 'Generando reporte...',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });

        }
    </script>
@endpush
