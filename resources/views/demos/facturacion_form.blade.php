{{-- file: resources/views/demos/facturacion/create.blade.php --}}
@extends('layouts.demo')
@section('title', 'Nueva Factura CFDI 4.0 — Demo')

@section('content')
    <a href="{{ route('demo.facturacion') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-brand transition-colors mb-5">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>Volver a Facturación
    </a>
    <div x-data="{ open:true }" x-show="open" class="flex items-start justify-between gap-3 p-4 rounded-xl bg-info/10 border border-info/20 text-info mb-5">
        <div class="flex items-start gap-2.5"><span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">info</span>
            <p class="text-sm font-medium"><strong>Modo Demo:</strong> Simulación de SAT CFDI 4.0. Los datos no se timbran realmente.</p></div>
        <button @click="open=false" class="shrink-0 text-info/60 hover:text-info"><span class="material-symbols-outlined text-[18px]">close</span></button>
    </div>
    <div x-data="facturaForm()">
        <form @submit.prevent="timbrarFactura">

            {{-- ── Tabs ──────────────────────────────────────────────────────── --}}
            <div class="flex gap-1 p-1 bg-gray-100 dark:bg-white/[0.04] rounded-2xl mb-5 overflow-x-auto">
                @foreach([['person','Cliente','cliente'],['request_quote','Cotización','cotizacion'],['inventory_2','Conceptos','conceptos'],['payments','Pago','pago']] as [$ic,$lbl,$tab])
                <button type="button" @click="activeTab='{{ $tab }}'"
                        :class="activeTab==='{{ $tab }}'
                            ? 'bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white shadow-sm'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex-shrink-0">
                    <span class="material-symbols-outlined text-[15px]">{{ $ic }}</span>{{ $lbl }}
                </button>
                @endforeach
            </div>

            {{-- ── Tab: Cliente ─────────────────────────────────────────────── --}}
            <div x-show="activeTab==='cliente'">
                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] mb-5">
                    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                        <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">person</span>
                        <h3 class="font-black text-gray-900 dark:text-white text-sm">Datos del Cliente (Receptor)</h3>
                    </div>
                    <div class="p-5 grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Seleccionar Cliente</label>
                            <div class="flex gap-2">
                                <select x-model="selectedClienteId" @change="cargarCliente" class="flex-1 px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="">-- Seleccione un cliente --</option>
                                    <template x-for="c in clientes" :key="c.id"><option :value="c.id" x-text="c.nombre+' ('+c.rfc+')'"></option></template>
                                </select>
                                <button type="button" @click="limpiarCliente" class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] text-gray-500 hover:border-danger hover:text-danger transition-all"><span class="material-symbols-outlined text-[18px]">refresh</span></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">RFC <span class="text-danger">*</span></label>
                            <input type="text" x-model="form.cliente.rfc" placeholder="AAA010101AAA" maxlength="13" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm uppercase focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Razón Social <span class="text-danger">*</span></label>
                            <input type="text" x-model="form.cliente.nombre" placeholder="Nombre o razón social" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Código Postal <span class="text-danger">*</span></label>
                            <input type="text" x-model="form.cliente.cp" placeholder="45079" maxlength="5" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Uso CFDI <span class="text-danger">*</span></label>
                            <select x-model="form.cliente.uso_cfdi" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                <option value="">Seleccionar...</option>
                                <option value="G01">G01 - Adquisición de mercancías</option>
                                <option value="G03">G03 - Gastos en general</option>
                                <option value="I01">I01 - Construcciones</option>
                                <option value="I04">I04 - Equipo de cómputo</option>
                                <option value="P01">P01 - Por definir</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Cotización ──────────────────────────────────────────── --}}
            <div x-show="activeTab==='cotizacion'">
                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] mb-5">
                    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                        <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">request_quote</span>
                        <h3 class="font-black text-gray-900 dark:text-white text-sm">Selección de Cotización</h3>
                    </div>
                    <div class="p-5">
                        <div class="mb-4">
                            <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Cotización</label>
                            <div class="flex gap-2">
                                <select x-model="selectedCotizacionId" @change="cargarCotizacion" :disabled="!form.cliente.rfc"
                                        class="flex-1 px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors disabled:opacity-50">
                                    <option value="">-- Seleccione --</option>
                                    <template x-for="cot in cotizacionesFiltradas" :key="cot.id"><option :value="cot.id" x-text="cot.folio+' — '+fmt(cot.total)+' ('+cot.fecha+')'"></option></template>
                                </select>
                                <button type="button" @click="limpiarCotizacion" class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] text-gray-500 hover:border-danger hover:text-danger transition-all"><span class="material-symbols-outlined text-[18px]">refresh</span></button>
                            </div>
                            <p x-show="!form.cliente.rfc" class="text-xs text-gray-400 mt-1">* Seleccione un cliente primero</p>
                        </div>
                        <template x-if="cotizacionSeleccionada">
                            <div class="p-4 bg-brand/5 rounded-xl border border-brand/10">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                    <div><p class="text-xs text-gray-400 mb-0.5">Folio</p><strong class="text-sm text-gray-900 dark:text-white" x-text="cotizacionSeleccionada.folio"></strong></div>
                                    <div><p class="text-xs text-gray-400 mb-0.5">Fecha</p><strong class="text-sm text-gray-900 dark:text-white" x-text="cotizacionSeleccionada.fecha"></strong></div>
                                    <div><p class="text-xs text-gray-400 mb-0.5">Vigencia</p><strong class="text-sm text-gray-900 dark:text-white" x-text="cotizacionSeleccionada.vigencia"></strong></div>
                                    <div><p class="text-xs text-gray-400 mb-0.5">Total</p><strong class="text-lg font-black text-brand" x-text="fmt(cotizacionSeleccionada.total)"></strong></div>
                                </div>
                                <button type="button" @click="convertirCotizacionAFactura"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-success/10 text-success border border-success/20 hover:bg-success/20 transition-all">
                                    <span class="material-symbols-outlined text-[15px]">receipt_long</span>Usar para facturar
                                </button>
                            </div>
                        </template>
                        <template x-if="!cotizacionSeleccionada && form.cliente.rfc">
                            <div class="text-center py-10 text-gray-400"><span class="material-symbols-outlined text-4xl block mb-2">request_quote</span>Sin cotizaciones para este cliente</div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Conceptos ───────────────────────────────────────────── --}}
            <div x-show="activeTab==='conceptos'">
                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] mb-5">
                    <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">inventory_2</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Conceptos</h3>
                        </div>
                        <button type="button" @click="addConcepto" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-brand text-white hover:bg-primary-700 transition-all"><span class="material-symbols-outlined text-[14px]">add</span>Agregar</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 dark:bg-white/[0.03]">
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center w-20">Cant.</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Clave SAT</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Unidad</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Descripción</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Precio Unit.</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Importe</th>
                                <th class="px-4 py-3 w-10"></th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                                <template x-for="(c,i) in form.conceptos" :key="i">
                                    <tr>
                                        <td class="px-4 py-3"><input type="number" x-model="c.cantidad" @input="calcularConcepto(i)" min="0.000001" step="0.000001" class="w-full px-2 py-1.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></td>
                                        <td class="px-4 py-3">
                                            <select x-model="c.clave_sat" class="w-full px-2 py-1.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                                <option value="01010101">01010101</option><option value="43201500">43201500</option>
                                                <option value="43221600">43221600</option><option value="27111500">27111500</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select x-model="c.unidad" class="w-full px-2 py-1.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                                <option value="H87">H87-Pieza</option><option value="EA">EA-Elemento</option>
                                                <option value="KGM">KGM-Kg</option><option value="LTR">LTR-Litro</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3"><input type="text" x-model="c.descripcion" placeholder="Descripción" class="w-full px-2 py-1.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></td>
                                        <td class="px-4 py-3"><div class="flex items-center gap-1"><span class="text-gray-400 text-sm">$</span><input type="number" x-model="c.valor_unitario" @input="calcularConcepto(i)" step="0.01" min="0" class="w-full px-2 py-1.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-right focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></div></td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white" x-text="fmt(c.importe)"></td>
                                        <td class="px-4 py-3 text-center"><button type="button" @click="removeConcepto(i)" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-danger/10 hover:text-danger transition-all"><span class="material-symbols-outlined text-[16px]">close</span></button></td>
                                    </tr>
                                </template>
                                <tr x-show="form.conceptos.length===0">
                                    <td colspan="7" class="px-4 py-10 text-center text-gray-400"><span class="material-symbols-outlined text-4xl block mb-2">inventory_2</span>Sin conceptos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-100 dark:border-white/[0.07]">
                        <div class="ml-auto max-w-xs space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Subtotal:</span><strong x-text="fmt(subtotal)"></strong></div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>IVA (16%):</span><strong x-text="fmt(iva)"></strong></div>
                            <div class="flex justify-between text-gray-900 dark:text-white font-black text-base border-t border-gray-100 dark:border-white/[0.06] pt-2">
                                <span>Total:</span><span x-text="fmt(total)" class="text-brand"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Pago ────────────────────────────────────────────────── --}}
            <div x-show="activeTab==='pago'">
                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] mb-5">
                    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                        <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">payments</span>
                        <h3 class="font-black text-gray-900 dark:text-white text-sm">Datos de Pago e Impuestos</h3>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Forma de Pago <span class="text-danger">*</span></label>
                                <select x-model="form.pago.forma_pago" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="01">01 - Efectivo</option>
                                    <option value="02">02 - Cheque</option>
                                    <option value="03">03 - Transferencia electrónica</option>
                                    <option value="04">04 - Tarjeta crédito</option>
                                    <option value="28">28 - Tarjeta débito</option>
                                    <option value="99">99 - Por definir</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Método <span class="text-danger">*</span></label>
                                <select x-model="form.pago.metodo_pago" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="PUE">PUE - Una sola exhibición</option>
                                    <option value="PPD">PPD - Parcialidades</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Moneda</label>
                                <select x-model="form.pago.moneda" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="MXN">MXN - Peso Mexicano</option>
                                    <option value="USD">USD - Dólar</option>
                                    <option value="EUR">EUR - Euro</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <p class="text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Configuración IVA</p>
                            <div class="grid sm:grid-cols-2 gap-3">
                                <div class="p-4 bg-gray-50 dark:bg-white/[0.03] rounded-xl">
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">IVA</p>
                                    <select x-model="form.impuestos.iva.tasa" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors mb-2">
                                        <option value="0">0% - Exento</option><option value="8">8% - Frontera</option><option value="16">16% - General</option>
                                    </select>
                                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                        <input type="checkbox" x-model="form.impuestos.iva.trasladado" class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand/30">
                                        Trasladado (incluido en precio)
                                    </label>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-white/[0.03] rounded-xl">
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Retenciones</p>
                                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer mb-2">
                                        <input type="checkbox" x-model="form.impuestos.retenciones.isr" class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand/30">
                                        Retener ISR (10%)
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                        <input type="checkbox" x-model="form.impuestos.retenciones.iva" class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand/30">
                                        Retener IVA (2/3 partes)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Footer acciones ─────────────────────────────────────────── --}}
            <div class="flex flex-wrap items-center justify-end gap-2 pt-3">
                <button type="button" onclick="history.back()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/[0.10] text-gray-600 dark:text-gray-400 hover:border-danger hover:text-danger transition-all">
                    <span class="material-symbols-outlined text-[18px]">close</span>Cancelar
                </button>
                <button type="button" @click="validarCFDI" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-info/10 text-info border border-info/20 hover:bg-info/20 transition-all">
                    <span class="material-symbols-outlined text-[18px]">verified</span>Validar CFDI
                </button>
                <button type="button" @click="guardarBorrador" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-warning/10 text-warning border border-warning/20 hover:bg-warning/20 transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>Guardar Borrador
                </button>
                <button type="submit" :disabled="!isValid" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-success/10 text-success border border-success/20 hover:bg-success/20 transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-[18px]">cloud_upload</span>Timbrar CFDI (Demo)
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
<script>
function facturaForm() {
    return {
        activeTab: 'cliente',
        form:{
            emisor:{rfc:'EKU9003173C9',nombre:'ESCUELA KEMPER URGATE',regimen_fiscal:'601',cp:'45079'},
            cliente:{rfc:'',nombre:'',cp:'',uso_cfdi:'',regimen_fiscal:''},
            conceptos:[],
            impuestos:{iva:{tasa:16,trasladado:true},ieps:{tasa:0,aplicar:false},retenciones:{isr:false,iva:false}},
            pago:{forma_pago:'03',metodo_pago:'PUE',moneda:'MXN',tipo_cambio:1,cuenta:'',complemento:false}
        },
        clientes:[ {id:1,rfc:'JCM990101ABC',nombre:'JCB Maquinarias',cp:'45050',uso_cfdi:'G01',regimen:'601'}, {id:2,rfc:'GAV860101XYZ',nombre:'Gavsa Industrial',cp:'45070',uso_cfdi:'G03',regimen:'601'}, {id:3,rfc:'JGV880202DEF',nombre:'JGV Construcciones',cp:'45100',uso_cfdi:'I01',regimen:'601'}, {id:4,rfc:'MAB930315GHI',nombre:'Maquinados del Bajío',cp:'45180',uso_cfdi:'G01',regimen:'601'}, {id:5,rfc:'TEC851212JKL',nombre:'Tecnología Aplicada',cp:'45138',uso_cfdi:'I04',regimen:'603'} ],
        cotizaciones:[ {id:101,cliente_id:1,folio:'COT-2024-001',fecha:'2024-01-15',vigencia:'2024-02-15',total:15600, conceptos:[{cantidad:2,clave_sat:'43201500',unidad:'H87',descripcion:'Taladro industrial',valor_unitario:5600,importe:11200},{cantidad:4,clave_sat:'43221600',unidad:'H87',descripcion:'Martillo neumático',valor_unitario:1100,importe:4400}]}, {id:102,cliente_id:2,folio:'COT-2024-008',fecha:'2024-01-20',vigencia:'2024-02-20',total:23400,conceptos:[{cantidad:3,clave_sat:'26111600',unidad:'H87',descripcion:'Motor eléctrico 5HP',valor_unitario:7800,importe:23400}]}, {id:103,cliente_id:3,folio:'COT-2024-022',fecha:'2024-02-05',vigencia:'2024-03-05',total:45600,conceptos:[{cantidad:1,clave_sat:'43201500',unidad:'H87',descripcion:'Taladro de columna',valor_unitario:18500,importe:18500},{cantidad:2,clave_sat:'27111500',unidad:'H87',descripcion:'Compresor industrial',valor_unitario:13550,importe:27100}]} ],
        selectedClienteId:'', selectedCotizacionId:'', cotizacionSeleccionada:null,
        init(){ this.addConcepto(); },
        get cotizacionesFiltradas(){ return this.selectedClienteId?this.cotizaciones.filter(c=>c.cliente_id==this.selectedClienteId):[]; },
        cargarCliente(){ const c=this.clientes.find(c=>c.id==this.selectedClienteId); if(c)this.form.cliente={rfc:c.rfc,nombre:c.nombre,cp:c.cp,uso_cfdi:c.uso_cfdi,regimen_fiscal:c.regimen}; },
        limpiarCliente(){ this.selectedClienteId='';this.form.cliente={rfc:'',nombre:'',cp:'',uso_cfdi:'',regimen_fiscal:''};this.limpiarCotizacion(); },
        cargarCotizacion(){ this.cotizacionSeleccionada=this.cotizaciones.find(c=>c.id==this.selectedCotizacionId)||null; },
        limpiarCotizacion(){ this.selectedCotizacionId='';this.cotizacionSeleccionada=null; },
        convertirCotizacionAFactura(){ if(!this.cotizacionSeleccionada)return; this.form.conceptos=this.cotizacionSeleccionada.conceptos.map(c=>({...c})); Swal.fire({title:'✅ Conceptos cargados',html:`${this.form.conceptos.length} conceptos de <strong>${this.cotizacionSeleccionada.folio}</strong>`,icon:'success',timer:2500,showConfirmButton:false}); this.activeTab='conceptos'; },
        addConcepto(){ this.form.conceptos.push({cantidad:1,clave_sat:'01010101',unidad:'H87',descripcion:'',valor_unitario:0,importe:0}); },
        removeConcepto(i){ this.form.conceptos.splice(i,1); },
        calcularConcepto(i){ const c=this.form.conceptos[i];c.importe=(c.cantidad||0)*(c.valor_unitario||0); },
        get subtotal(){ return this.form.conceptos.reduce((s,c)=>s+(c.importe||0),0); },
        get iva(){ return this.form.impuestos.iva.trasladado?this.subtotal*(this.form.impuestos.iva.tasa/100):0; },
        get total(){ return this.subtotal+this.iva; },
        get isValid(){ return this.form.emisor.rfc&&this.form.cliente.rfc&&this.form.cliente.nombre&&this.form.cliente.cp&&this.form.cliente.uso_cfdi&&this.form.conceptos.length>0&&this.form.conceptos.every(c=>c.descripcion&&c.valor_unitario>0); },
        fmt(v){ return new Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN',minimumFractionDigits:2}).format(v||0); },
        validarCFDI(){ if(!this.isValid){Swal.fire({title:'❌ Error',text:'Completa todos los campos requeridos.',icon:'error'});return;} Swal.fire({title:'✅ CFDI Válido',html:`RFC Emisor: <strong>${this.form.emisor.rfc}</strong><br>RFC Receptor: <strong>${this.form.cliente.rfc}</strong><br>Conceptos: <strong>${this.form.conceptos.length}</strong><br>Total: <strong>${this.fmt(this.total)}</strong>`,icon:'success'}); },
        guardarBorrador(){ Swal.fire({title:'📝 Borrador guardado',text:'Guardado en modo demo.',icon:'info',timer:2000,showConfirmButton:false}); },
        timbrarFactura(){ if(!this.isValid){Swal.fire({title:'❌ Error',text:'Completa todos los campos.',icon:'error'});return;} Swal.fire({title:'🔄 Timbrando...',html:'Procesando con SAT...',didOpen:()=>{Swal.showLoading();setTimeout(()=>{Swal.close();const uuid='xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,c=>{const r=Math.random()*16|0;return(c==='x'?r:r&0x3|0x8).toString(16)});Swal.fire({title:'✅ CFDI Timbrado',html:`<p><strong>UUID:</strong> ${uuid}</p><p><strong>Total:</strong> ${this.fmt(this.total)}</p><p><strong>Fecha:</strong> ${new Date().toLocaleString()}</p>`,icon:'success',showCancelButton:true,confirmButtonText:'Ver listado',cancelButtonText:'Nueva factura'}).then(r=>{if(r.isConfirmed)window.location.href='{{ route("demo.facturacion") }}';else window.location.reload();});},3000);},allowOutsideClick:false}); },
    };
}
</script>
@endpush
