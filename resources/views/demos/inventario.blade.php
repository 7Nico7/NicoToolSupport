@extends('layouts.demo')
@section('title', 'Gestión de Inventario — Demo')

@section('content')

    <div class="px-6 py-6">

        @php
            // 1. Datos de Inventario
            $categorias = ['Herramientas Eléctricas', 'Herramientas Manuales', 'Equipo de Seguridad', 'Material Eléctrico', 'Fontanería'];
            $marcas = ['Bosch', 'Stanley', 'DeWalt', 'Makita', 'Truper'];
            $nombres = ['Taladro Percutor', 'Martillo Demoledor', 'Sierra Circular', 'Lijadora Orbital', 'Pulidora Angular'];

            $productosData = [];

            for ($i = 1; $i <= 50; $i++) {
                $stockActual = $i % 2 === 0 ? rand(0, 5) : rand(15, 500);
                $stockMinimo = 15;
                $estado = ($stockActual == 0) ? 'Agotado' : (($stockActual < $stockMinimo) ? 'Crítico' : 'Normal');

                $productosData[] = (object) [
                    'id' => $i,
                    'sku' => 'SKU-' . chr(rand(65, 90)) . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'nombre' => $nombres[($i - 1) % count($nombres)] . ' ' . $marcas[array_rand($marcas)],
                    'categoria' => $categorias[array_rand($categorias)],
                    'stock' => $stockActual,
                    'estado' => $estado,
                    'precio' => rand(500, 5000) + (rand(0, 99) / 100),
                ];
            }

            $productos = collect($productosData);

            /* ========================
                STATS (Para x-demo.stats-cards)
            ======================== */
            $stats = [
                [
                    'label' => 'Total SKU',
                    'value' => $productos->count(),
                    'icon' => 'inventory_2',
                    'color' => 'primary',
                    'subtext' => 'productos registrados',
                ],
                [
                    'label' => 'Valor Inventario',
                    'value' => '$' . number_format($productos->sum(fn($p) => $p->stock * ($p->precio / 1.3)), 0),
                    'icon' => 'payments',
                    'color' => 'success',
                    'subtext' => 'estimado (costo)',
                ],
                [
                    'label' => 'Stock Crítico',
                    'value' => $productos->whereIn('estado', ['Crítico', 'Agotado'])->count(),
                    'icon' => 'report_problem',
                    'color' => 'danger',
                    'subtext' => 'requiere atención',
                ],
                [
                    'label' => 'Unidades Totales',
                    'value' => number_format($productos->sum('stock')),
                    'icon' => 'layers',
                    'color' => 'info',
                    'subtext' => 'en existencia',
                ],
            ];

            /* ========================
                COLUMNAS TABLA (Para x-demo.table)
            ======================== */
            $columns = [
                [
                    'field' => 'sku',
                    'label' => 'SKU',
                    'format' => 'code',
                ],
                [
                    'field' => 'nombre',
                    'label' => 'Producto',
                ],
                [
                    'field' => 'categoria',
                    'label' => 'Categoría',
                ],
                [
                    'field' => 'stock',
                    'label' => 'Stock',
                    'align' => 'center',
                ],
                [
                    'field' => 'estado',
                    'label' => 'Estado',
                    'format' => 'badge',
                ],
                [
                    'field' => 'precio',
                    'label' => 'Precio Venta',
                    'align' => 'right',
                ],
            ];
        @endphp

        {{-- Uso de los componentes correctos con el prefijo "demo." --}}
        <x-demo.stats-cards :stats="$stats" module="inventario" />

<div class="mt-8">
    <x-demo.table
        :items="$productos"
        :columns="$columns"
        title="Catálogo Maestro de Inventario"
        searchPlaceholder="Buscar por SKU o nombre..."
        :itemsPerPage="10"
    >
        <x-slot:actions>

<a href="{{ route('demo.inventario.form') }}"
   class="flex items-center gap-2 bg-brand text-white px-4 py-2 rounded-xl font-bold hover:bg-brand/90 transition-all text-sm inline-flex">
    <span class="material-symbols-outlined text-[20px]">add</span>
    Nuevo Producto
</a>
        </x-slot:actions>
    </x-demo.table>
</div>

@endsection
