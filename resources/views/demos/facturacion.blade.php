@extends('layouts.demo')

@section('title', 'Demo - Facturación')

@section('content')
<div class="px-6 py-6">

@php
/* ===========================
   GENERAR DATOS DE DEMO
=========================== */

$facturasData = [];

$clientes = [
'JCB Maquinarias','Gavsa Industrial','JGV Construcciones','Constructora ABC',
'Logística Express','Tech Solutions','Minera del Norte','Agroindustrias López',
'Transportes Unidos','Inmobiliaria Central','Hotelera del Pacífico',
'Ferretería El Constructor','Taller Mecánico Rápido','Distribuidora de Alimentos',
'Textiles Modernos',
];

$tipos = ['factura','nota_credito','recibo','complemento'];
$metodos_pago = ['Efectivo','Transferencia','Tarjeta','Cheque','Crédito'];
$estados = ['pagada','pendiente','vencida','cancelada','parcial'];

$fechaInicio = strtotime('-4 months');
$fechaFin = time();

for ($i=1;$i<=40;$i++) {

$uuid = sprintf(
'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
mt_rand(0,0xffff),mt_rand(0,0xffff),
mt_rand(0,0xffff),
mt_rand(0,0x0fff)|0x4000,
mt_rand(0,0x3fff)|0x8000,
mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff)
);

$folio='F'.str_pad(rand(1,999),3,'0',STR_PAD_LEFT).'-'.str_pad($i,6,'0',STR_PAD_LEFT);

$fechaTimestamp = rand($fechaInicio,$fechaFin);
$fecha_emision=date('Y-m-d',$fechaTimestamp);
$fecha_vencimiento=date('Y-m-d',strtotime($fecha_emision.' + '.rand(15,60).' days'));

$cliente=$clientes[array_rand($clientes)];
$rfc=substr(strtoupper(str_replace(' ','',$cliente)),0,3).rand(100101,999999).'ABC';

$tipo=$tipos[array_rand($tipos)];
$estado=$estados[array_rand($estados)];
$metodo_pago=$metodos_pago[array_rand($metodos_pago)];

$subtotal = rand(5000,250000)+rand(0,99)/100;
$iva=$subtotal*0.16;
$total=$subtotal+$iva;

$facturasData[]=(object)[
'id'=>$i,
'uuid'=>$uuid,
'folio'=>$folio,
'tipo'=>$tipo,
'cliente'=>$cliente,
'rfc'=>$rfc,
'fecha_emision'=>$fecha_emision,
'fecha_vencimiento'=>$fecha_vencimiento,
'subtotal'=>$subtotal,
'iva'=>$iva,
'total'=>$total,
'estado'=>$estado,
'metodo_pago'=>$metodo_pago,
];
}

usort($facturasData,function($a,$b){
return strtotime($b->fecha_emision)-strtotime($a->fecha_emision);
});

$facturas=collect($facturasData);

/* ===========================
   STATS
=========================== */

$totalFacturas=$facturas->count();
$totalPagadas=$facturas->where('estado','pagada')->count();
$totalPendientes=$facturas->where('estado','pendiente')->count();
$totalVencidas=$facturas->where('estado','vencida')->count();

$montoTotal=$facturas->sum('total');
$montoPendiente=$facturas->whereIn('estado',['pendiente','vencida'])->sum('total');

$stats=[

[
'label'=>'Total Facturas',
'value'=>$totalFacturas,
'icon'=>'file-invoice',
'color'=>'primary',
'subtext'=>'documentos'
],

[
'label'=>'Facturado',
'value'=>'$'.number_format($montoTotal,2),
'icon'=>'dollar-sign',
'color'=>'success',
'subtext'=>'MXN'
],

[
'label'=>'Pendiente Cobro',
'value'=>'$'.number_format($montoPendiente,2),
'icon'=>'clock',
'color'=>'warning',
'subtext'=>'MXN'
],

[
'label'=>'Por Cobrar',
'value'=>$totalPendientes+$totalVencidas,
'icon'=>'exclamation-circle',
'color'=>'danger',
'subtext'=>'facturas',
'percentage'=>$totalFacturas>0
? round((($totalPendientes+$totalVencidas)/$totalFacturas)*100)
:0
],

];

/* ===========================
   COLUMNAS TABLA
=========================== */

$columns=[

['field'=>'folio','label'=>'Folio','format'=>'code','align'=>'left'],

['field'=>'fecha_emision','label'=>'Fecha','align'=>'center'],

['field'=>'cliente','label'=>'Cliente'],

['field'=>'rfc','label'=>'RFC'],

['field'=>'total','label'=>'Total','format'=>'currency','align'=>'right'],

['field'=>'estado','label'=>'Estado','format'=>'badge','align'=>'center'],

['field'=>'tipo','label'=>'Tipo','format'=>'badge','align'=>'center'],

['field'=>'metodo_pago','label'=>'Pago','align'=>'center'],

];

@endphp


{{-- ========================
    STATS CARDS
======================== --}}

<x-demo.stats-cards
:stats="$stats"
module="facturacion"
/>



{{-- ========================
    ALERTAS
======================== --}}

<div class="grid md:grid-cols-2 gap-4 mt-6">

<div class="bg-warning/10 border border-warning/20 rounded-xl p-4 flex items-center gap-3">
<span class="material-symbols-outlined text-warning">warning</span>
<div>
<p class="font-semibold text-warning">{{ $totalVencidas }} facturas vencidas</p>
<p class="text-xs text-gray-500">requieren atención</p>
</div>
</div>

<div class="bg-success/10 border border-success/20 rounded-xl p-4 flex items-center gap-3">
<span class="material-symbols-outlined text-success">check_circle</span>
<div>
<p class="font-semibold text-success">{{ $totalPagadas }} facturas pagadas</p>
<p class="text-xs text-gray-500">en el período</p>
</div>
</div>

</div>


{{-- ========================
    TABLA
======================== --}}

<div class="mt-6">

<x-demo.table
:items="$facturas"
:columns="$columns"
title="Comprobantes Fiscales"
searchPlaceholder="Buscar factura..."
module="facturacion"
:itemsPerPage="10"
showPagination="true"
exportable="true"
/>

</div>

</div>
@endsection
