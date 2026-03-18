{{-- file: resources/views/demos/cotizaciones/create.blade.php --}}
@extends('layouts.demo')
@section('title', 'Nueva Cotización — Demo')

@section('content')
    <a href="{{ route('demo.cotizaciones') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-brand transition-colors mb-5">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>Volver a Cotizaciones
    </a>
    <div x-data="{ open:true }" x-show="open" class="flex items-start justify-between gap-3 p-4 rounded-xl bg-info/10 border border-info/20 text-info mb-5">
        <div class="flex items-start gap-2.5"><span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">info</span>
            <p class="text-sm font-medium"><strong>Modo Demo:</strong> Esta es una interfaz de demostración. Los datos no se guardarán.</p></div>
        <button @click="open=false" class="shrink-0 text-info/60 hover:text-info"><span class="material-symbols-outlined text-[18px]">close</span></button>
    </div>
    <div x-data="cotizacionForm()">
        <form @submit.prevent="saveCotizacion">
            <div class="grid lg:grid-cols-3 gap-5">

                {{-- ── MAIN ────────────────────────────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Cliente --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">person</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Información del Cliente</h3>
                        </div>
                        <div class="p-5 grid sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Cliente <span class="text-danger">*</span></label>
                                <select x-model="form.cliente_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="">Seleccionar cliente...</option>
                                    <option value="1">JCB Maquinarias</option>
                                    <option value="2">Gavsa Industrial</option>
                                    <option value="3">JGV Construcciones</option>
                                    <option value="4">Constructora ABC</option>
                                    <option value="5">Logística Express</option>
                                    <option value="6">Tech Solutions</option>
                                    <option value="7">Minera del Norte</option>
                                    <option value="8">Agroindustrias López</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Fecha</label>
                                <input type="date" x-model="form.fecha" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Validez</label>
                                <select x-model="form.validez" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="7">7 días</option><option value="15">15 días</option>
                                    <option value="30">30 días</option><option value="45">45 días</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Notas</label>
                                <textarea x-model="form.notas" rows="2" placeholder="Notas adicionales..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">inventory_2</span>
                                <h3 class="font-black text-gray-900 dark:text-white text-sm">Productos / Servicios</h3>
                            </div>
                            <button type="button" @click="addItem" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-brand text-white hover:bg-primary-700 transition-all">
                                <span class="material-symbols-outlined text-[14px]">add</span>Agregar
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="bg-gray-50 dark:bg-white/[0.03]">
                                    <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left" style="width:40%">Producto</th>
                                    <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center" style="width:12%">Cant.</th>
                                    <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right" style="width:22%">Precio Unit.</th>
                                    <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right" style="width:18%">Subtotal</th>
                                    <th class="px-4 py-3" style="width:40px"></th>
                                </tr></thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                                    <template x-for="(item,i) in form.items" :key="i">
                                        <tr>
                                            <td class="px-4 py-3">
                                                <select x-model="item.producto_id" @change="updateItem(i)"
                                                        class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                                    <option value="">Seleccionar...</option>
                                                    <option value="101">Martillo Hidráulico HB2000</option>
                                                    <option value="102">Cucharon Retroexcavadora 1m³</option>
                                                    <option value="103">Kit de Mantenimiento</option>
                                                    <option value="104">Aceite Hidráulico 20L</option>
                                                    <option value="105">Filtros de Aire</option>
                                                    <option value="106">Servicio de Instalación</option>
                                                    <option value="107">Garantía Extendida 2 años</option>
                                                </select>
                                                <p x-show="item.descripcion" x-text="item.descripcion" class="text-xs text-gray-400 mt-1"></p>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" x-model="item.cantidad" min="1" step="1"
                                                       class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-1">
                                                    <span class="text-gray-400 text-sm">$</span>
                                                    <input type="number" x-model="item.precio_unitario" step="0.01" min="0"
                                                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-right focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white" x-text="fmt(item.cantidad*item.precio_unitario)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="removeItem(i)" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-danger/10 hover:text-danger transition-all">
                                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="form.items.length===0">
                                        <td colspan="5" class="px-4 py-10 text-center text-gray-400 dark:text-gray-600">
                                            <span class="material-symbols-outlined text-4xl block mb-2">inventory_2</span>
                                            Sin items. Haz clic en Agregar para comenzar.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ── SIDEBAR ──────────────────────────────────────────────────── --}}
                <div class="space-y-5">

                    {{-- Resumen financiero --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">summarize</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Resumen</h3>
                        </div>
                        <div class="p-5 space-y-3">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Vendedor</label>
                                <select x-model="form.vendedor" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option>Carlos Rodríguez</option><option>María Pérez</option>
                                    <option>Juan Vásquez</option><option>Ana González</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Condiciones de pago</label>
                                <select x-model="form.condiciones_pago" @change="resetIntereses"
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="contado">Contado</option>
                                    <option value="credito">Crédito</option>
                                </select>
                            </div>
                            <template x-if="form.condiciones_pago==='credito'">
                                <div class="p-3 bg-gray-50 dark:bg-white/[0.03] rounded-xl space-y-3">
                                    <div>
                                        <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Plazo</label>
                                        <select x-model="form.plazo_meses" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                            <template x-for="m in [1,2,3,6,9,12]" :key="m"><option :value="m" x-text="m+' mes(es)'"></option></template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Tasa mensual (%)</label>
                                        <input type="number" x-model="form.tasa_interes" step="0.1" min="0"
                                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-right focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    </div>
                                </div>
                            </template>
                            <hr class="border-gray-100 dark:border-white/[0.06]">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Items:</span><strong x-text="form.items.length"></strong></div>
                                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Subtotal:</span><strong x-text="fmt(subtotal)"></strong></div>
                                <template x-if="form.condiciones_pago!=='contado'">
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Intereses:</span><strong x-text="fmt(interesTotal)"></strong></div>
                                </template>
                                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>IVA (16%):</span><strong x-text="fmt(iva)"></strong></div>
                                <div class="flex justify-between text-gray-900 dark:text-white font-black text-base border-t border-gray-100 dark:border-white/[0.06] pt-2">
                                    <span>Total:</span><span x-text="fmt(total)" class="text-brand"></span>
                                </div>
                                <template x-if="form.condiciones_pago!=='contado'&&form.items.length>0">
                                    <div class="p-2.5 bg-brand/5 rounded-xl text-xs text-center text-gray-600 dark:text-gray-400">
                                        <span x-text="form.plazo_meses+' cuotas de '+fmt(cuotaMensual)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] p-5 space-y-2.5">
                        <button type="submit" :disabled="!isValid"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-brand text-white hover:bg-primary-700 transition-all shadow-sm shadow-brand/20 disabled:opacity-50">
                            <span class="material-symbols-outlined text-[18px]">save</span>Guardar Cotización
                        </button>
                        <button type="button" @click="sendByEmail"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/[0.10] text-gray-600 dark:text-gray-400 hover:border-success hover:text-success transition-all">
                            <span class="material-symbols-outlined text-[18px]">email</span>Enviar por Email
                        </button>
                    </div>

                    {{-- Ejemplo --}}
                    <div class="bg-warning/10 border border-warning/20 rounded-2xl p-4">
                        <div class="flex items-center gap-2 mb-3"><span class="material-symbols-outlined text-warning text-[18px]">lightbulb</span><p class="text-sm font-bold text-warning">Ejemplo Rápido</p></div>
                        <button type="button" @click="loadExample" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold bg-warning/20 text-warning hover:bg-warning/30 transition-all">
                            <span class="material-symbols-outlined text-[15px]">science</span>Cargar ejemplo
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
function cotizacionForm() {
    return {
        form:{ cliente_id:'', fecha:new Date().toISOString().split('T')[0], validez:'15', notas:'', vendedor:'Carlos Rodríguez', condiciones_pago:'contado', plazo_meses:6, tasa_interes:2.5, items:[] },
        productos:{ '101':{nombre:'Martillo Hidráulico HB2000',precio:12500000.50,descripcion:'Para excavadora 20-30 ton'},'102':{nombre:'Cucharon Retroexcavadora 1m³',precio:5600000,descripcion:'Acero Hardox'},'103':{nombre:'Kit de Mantenimiento',precio:3200000,descripcion:'Filtros + aceite + grasa'},'104':{nombre:'Aceite Hidráulico 20L',precio:890000.75,descripcion:'ISO 68'},'105':{nombre:'Filtros de Aire',precio:450000.25,descripcion:'Juego 3 filtros'},'106':{nombre:'Servicio de Instalación',precio:1500000,descripcion:'Instalación en sitio'},'107':{nombre:'Garantía Extendida 2 años',precio:800000,descripcion:'Cobertura total'} },
        init(){ this.addItem(); },
        addItem(){ this.form.items.push({producto_id:'',cantidad:1,precio_unitario:0,descripcion:''}); },
        removeItem(i){ this.form.items.splice(i,1); },
        updateItem(i){ const p=this.productos[this.form.items[i].producto_id]; if(p){this.form.items[i].precio_unitario=p.precio;this.form.items[i].descripcion=p.descripcion;} },
        resetIntereses(){ if(this.form.condiciones_pago==='contado'){this.form.plazo_meses=6;this.form.tasa_interes=2.5;} },
        get subtotal(){ return this.form.items.reduce((s,i)=>s+(parseInt(i.cantidad)||0)*(parseFloat(i.precio_unitario)||0),0); },
        get interesTotal(){ return this.form.condiciones_pago==='contado'?0:this.subtotal*(this.form.tasa_interes/100)*this.form.plazo_meses; },
        get iva(){ return (this.subtotal+this.interesTotal)*0.16; },
        get total(){ return this.subtotal+this.interesTotal+this.iva; },
        get cuotaMensual(){ return this.form.condiciones_pago==='contado'?0:Math.round((this.subtotal+this.interesTotal)/this.form.plazo_meses); },
        get isValid(){ return this.form.cliente_id&&this.form.items.length>0&&this.form.items.every(i=>i.producto_id&&parseInt(i.cantidad)>0); },
        fmt(v){ return new Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN',minimumFractionDigits:2}).format(v||0); },
        loadExample(){ this.form.cliente_id='1';this.form.validez='30';this.form.condiciones_pago='credito';this.form.plazo_meses=12;this.form.tasa_interes=2.0;this.form.notas='Solicita financiamiento 12 meses.';this.form.items=[{producto_id:'101',cantidad:2,precio_unitario:12500000.50,descripcion:'Para excavadora 20-30 ton'},{producto_id:'104',cantidad:5,precio_unitario:890000.75,descripcion:'ISO 68'}];Swal.fire({title:'✅ Ejemplo cargado',icon:'success',timer:1500,showConfirmButton:false}); },
        saveCotizacion(){ if(!this.isValid){Swal.fire({title:'Campos requeridos',icon:'warning'});return;} Swal.fire({title:'✅ Cotización guardada',html:`Total: <strong>${this.fmt(this.total)}</strong><br><small class="text-gray-400">(Modo Demo)</small>`,icon:'success'}).then(()=>{if(confirm('¿Ver listado?'))window.location.href='{{ route("demo.cotizaciones") }}';}); },
        sendByEmail(){ const e=prompt('Correo:','cliente@ejemplo.com');if(e)Swal.fire({title:'✅ Enviado',html:`<strong>${e}</strong><br><small>(Demo)</small>`,icon:'success'}); },
    };
}
</script>
@endpush
