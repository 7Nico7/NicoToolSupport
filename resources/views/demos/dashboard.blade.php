{{-- file: resources/views/demos/dashboard.blade.php --}}
@extends('layouts.demo')
@section('title', 'Dashboard — Demo')

@section('content')
@php
    $mes = date('F Y');
    // ── KPIs ─────────────────────────────────────────────────────────────
    $kpis = [
        ['label'=>'Cotizaciones',    'value'=>rand(45,90),          'icon'=>'request_quote', 'color'=>'primary', 'change'=>'+'.rand(5,20).'%', 'trend'=>'up',   'link'=>'demo.cotizaciones'],
        ['label'=>'Facturas emitidas','value'=>rand(30,65),         'icon'=>'receipt_long',  'color'=>'success', 'change'=>'+'.rand(3,15).'%', 'trend'=>'up',   'link'=>'demo.facturacion'],
        ['label'=>'Valor en cotiz.', 'value'=>'$'.number_format(rand(500,2000)*1000,0),'icon'=>'attach_money','color'=>'info','change'=>'+'.rand(2,12).'%','trend'=>'up','link'=>'demo.cotizaciones'],
        ['label'=>'Productos en stock','value'=>rand(120,300),      'icon'=>'inventory_2',   'color'=>'warning', 'change'=>rand(0,1)?'-'.rand(1,8).'%':'+'.rand(1,5).'%', 'trend'=>rand(0,1)?'down':'up', 'link'=>'demo.inventario'],
    ];

    // ── Chart data (mocked months) ────────────────────────────────────
    $meses = ['Oct','Nov','Dic','Ene','Feb','Mar'];
    $cotizacionesData = [42,58,35,71,65,rand(50,85)];
    $facturasData     = [28,41,22,55,48,rand(35,65)];

    // ── Recent cotizaciones ───────────────────────────────────────────
    $clientes = ['JCB Maquinarias','Gavsa Industrial','JGV Construcciones','Constructora ABC','Logística Express'];
    $recientes = [];
    for($i=1;$i<=5;$i++){
        $monto=rand(80000,4500000);
        $estados=['aprobada','pendiente','enviada','borrador'];
        $recientes[]=(object)['folio'=>'COT-2024-'.str_pad(rand(100,999),3,'0',STR_PAD_LEFT),'cliente'=>$clientes[array_rand($clientes)],'fecha'=>date('d/m/Y',strtotime('-'.rand(0,30).' days')),'monto'=>$monto,'estado'=>$estados[array_rand($estados)]];
    }

    // ── Stock crítico ─────────────────────────────────────────────────
    $criticos=[
        (object)['nombre'=>'Aceite Hidráulico 20L',   'stock'=>2, 'minimo'=>10,'sku'=>'SKU-AH-001'],
        (object)['nombre'=>'Filtros de Aire',          'stock'=>0, 'minimo'=>5, 'sku'=>'SKU-FA-002'],
        (object)['nombre'=>'Cucharon 1m³',             'stock'=>1, 'minimo'=>3, 'sku'=>'SKU-CU-003'],
        (object)['nombre'=>'Kit Mantenimiento',        'stock'=>3, 'minimo'=>8, 'sku'=>'SKU-KM-004'],
    ];

    $twColors=['primary'=>['bg'=>'bg-primary-50 dark:bg-primary-950/60','ic'=>'text-brand dark:text-primary-400','bar'=>'bg-brand'],'success'=>['bg'=>'bg-success/10','ic'=>'text-success','bar'=>'bg-success'],'warning'=>['bg'=>'bg-warning/10','ic'=>'text-warning','bar'=>'bg-warning'],'info'=>['bg'=>'bg-info/10','ic'=>'text-info','bar'=>'bg-info']];
    $estBadge=['aprobada'=>'bg-success/10 text-success','pendiente'=>'bg-warning/10 text-warning','rechazada'=>'bg-danger/10 text-danger','borrador'=>'bg-gray-100 dark:bg-white/[0.06] text-gray-500','enviada'=>'bg-info/10 text-info'];
@endphp

{{-- ── Welcome banner ────────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6
            p-5 bg-gradient-to-r from-brand to-primary-500 rounded-2xl text-white">
    <div>
        <p class="text-sm font-medium text-white/70 mb-1">Dashboard — {{ $mes }}</p>
        <h1 class="text-xl font-black">Bienvenido al Demo</h1>
        <p class="text-white/80 text-sm mt-1">Explora el sistema: cotizaciones, facturación e inventario.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('demo.cotizaciones.create') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold
                  bg-white/20 hover:bg-white/30 text-white transition-all">
            <span class="material-symbols-outlined text-[15px]">add</span>Nueva cotización
        </a>
        <a href="{{ route('demo.facturacion.create') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold
                  bg-white/20 hover:bg-white/30 text-white transition-all">
            <span class="material-symbols-outlined text-[15px]">receipt_long</span>Nueva factura
        </a>
    </div>
</div>

{{-- ── KPI grid ────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($kpis as $kpi)
    @php $c=$twColors[$kpi['color']]; @endphp
    <a href="{{ route($kpi['link']) }}" class="block group">
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] p-5
                    hover:border-brand/30 hover:shadow-lg hover:shadow-brand/5 transition-all">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 {{ $c['bg'] }} rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined {{ $c['ic'] }} text-[20px]"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $kpi['icon'] }}</span>
                </div>
                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[11px] font-bold
                             {{ $kpi['trend']==='up' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                    <span class="material-symbols-outlined text-[12px]">
                        {{ $kpi['trend']==='up' ? 'trending_up' : 'trending_down' }}
                    </span>
                    {{ $kpi['change'] }}
                </span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white leading-none">{{ $kpi['value'] }}</p>
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 mt-1 uppercase tracking-wider group-hover:text-brand transition-colors">
                {{ $kpi['label'] }}
            </p>
        </div>
    </a>
    @endforeach
</div>

{{-- ── Charts + Acciones rápidas ─────────────────────────────────────────── --}}
<div class="grid lg:grid-cols-3 gap-5 mb-5">

    {{-- Chart principal --}}
    <div class="lg:col-span-2 bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">bar_chart</span>
                <h3 class="font-black text-gray-900 dark:text-white text-sm">Actividad últimos 6 meses</h3>
            </div>
            <div class="flex items-center gap-3 text-xs text-gray-400">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-brand"></span>Cotizaciones</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-success"></span>Facturas</span>
            </div>
        </div>
        <div class="p-5">
            <canvas id="actividadChart" class="w-full" height="220"></canvas>
        </div>
    </div>

    {{-- Acciones rápidas --}}
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">bolt</span>
            <h3 class="font-black text-gray-900 dark:text-white text-sm">Acciones Rápidas</h3>
        </div>
        <div class="p-5 space-y-2">
            @foreach([
                ['request_quote','Nueva Cotización','demo.cotizaciones.create','brand','text-brand','bg-primary-50 dark:bg-primary-950/60'],
                ['receipt_long','Nueva Factura','demo.facturacion.create','success','text-success','bg-success/10'],
                ['inventory_2','Nuevo Producto','demo.inventario.form','info','text-info','bg-info/10'],
                ['swap_horiz','Movimiento Inventario','demo.inventario.movimiento.create','warning','text-warning','bg-warning/10'],
                ['person_add','Nuevo Cliente','demo.clientes.create','violet','text-violet-600 dark:text-violet-400','bg-violet-50 dark:bg-violet-950/40'],
            ] as [$ic,$lbl,$route,$color,$tc,$bg])
            <a href="{{ route($route) }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/[0.04] transition-all group">
                <div class="w-9 h-9 {{ $bg }} rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined {{ $tc }} text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 300">{{ $ic }}</span>
                </div>
                <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-brand transition-colors">{{ $lbl }}</span>
                <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[16px] ml-auto group-hover:text-brand transition-colors">chevron_right</span>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Cotizaciones recientes + Stock crítico ──────────────────────────────── --}}
<div class="grid lg:grid-cols-2 gap-5">

    {{-- Cotizaciones recientes --}}
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">request_quote</span>
                <h3 class="font-black text-gray-900 dark:text-white text-sm">Cotizaciones Recientes</h3>
            </div>
            <a href="{{ route('demo.cotizaciones') }}"
               class="text-xs font-bold text-brand hover:underline">Ver todas →</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-white/[0.05]">
            @foreach($recientes as $r)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-brand dark:text-primary-400">{{ $r->folio }}</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $estBadge[$r->estado] }}">{{ $r->estado }}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $r->cliente }} — {{ $r->fecha }}</p>
                </div>
                <span class="text-sm font-black text-gray-900 dark:text-white whitespace-nowrap">${{ number_format($r->monto,0) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Stock crítico --}}
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-danger text-[20px]" style="font-variation-settings:'FILL' 1,'wght' 400">warning</span>
                <h3 class="font-black text-gray-900 dark:text-white text-sm">Stock Crítico</h3>
            </div>
            <a href="{{ route('demo.inventario') }}"
               class="text-xs font-bold text-brand hover:underline">Ver inventario →</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-white/[0.05]">
            @foreach($criticos as $p)
            @php $pct = $p->stock > 0 ? round($p->stock/$p->minimo*100) : 0; @endphp
            <div class="px-5 py-3">
                <div class="flex items-center justify-between mb-1">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $p->nombre }}</p>
                        <p class="text-[11px] text-gray-400 font-mono">{{ $p->sku }}</p>
                    </div>
                    <div class="text-right shrink-0 ml-3">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->stock===0 ? 'bg-gray-100 dark:bg-white/[0.06] text-gray-500' : 'bg-danger/10 text-danger' }}">
                            {{ $p->stock===0 ? 'Agotado' : 'Crítico' }}
                        </span>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $p->stock }}/{{ $p->minimo }} mín</p>
                    </div>
                </div>
                <div class="h-1.5 bg-gray-100 dark:bg-white/[0.06] rounded-full overflow-hidden">
                    <div class="h-full {{ $p->stock===0 ? 'bg-gray-300 dark:bg-white/[0.15]' : 'bg-danger' }} rounded-full transition-all"
                         style="width:{{ min(100,$pct) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-3 border-t border-gray-100 dark:border-white/[0.07]">
            <a href="{{ route('demo.inventario.movimiento.create') }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold
                      bg-danger/10 text-danger border border-danger/20 hover:bg-danger/20 transition-all">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>Registrar entrada
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#9ca3af' : '#6b7280';
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

    new Chart(document.getElementById('actividadChart'), {
        type: 'bar',
        data: {
            labels: @json($meses),
            datasets: [
                { label: 'Cotizaciones', data: @json($cotizacionesData),
                  backgroundColor: 'rgba(37,99,235,0.8)', borderRadius: 6, borderSkipped: false },
                { label: 'Facturas',     data: @json($facturasData),
                  backgroundColor: 'rgba(34,197,94,0.8)', borderRadius: 6, borderSkipped: false },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 11 } } },
                y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 11 } }, beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
