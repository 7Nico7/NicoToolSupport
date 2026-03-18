{{-- file: resources/views/demos/inventario/form.blade.php --}}
@extends('layouts.demo')
@section('title', 'Nuevo Producto — Demo')

@section('content')
    <a href="{{ route('demo.inventario') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-brand transition-colors mb-5">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>Volver a Inventario
    </a>
    <div x-data="{ open:true }" x-show="open" class="flex items-start justify-between gap-3 p-4 rounded-xl bg-info/10 border border-info/20 text-info mb-5">
        <div class="flex items-start gap-2.5"><span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">info</span>
            <p class="text-sm font-medium"><strong>Modo Demo:</strong> Esta es una interfaz de demostración. Los datos no se guardarán.</p></div>
        <button @click="open=false" class="shrink-0 text-info/60 hover:text-info"><span class="material-symbols-outlined text-[18px]">close</span></button>
    </div>
    <div x-data="productoForm()">
        <form @submit.prevent="saveProducto">
            <div class="grid lg:grid-cols-3 gap-5">

                {{-- MAIN --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Info básica --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">inventory_2</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Información del Producto</h3>
                        </div>
                        <div class="p-5 grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">SKU <span class="text-danger">*</span></label>
                                <input type="text" x-model="form.sku" placeholder="SKU-ABC-1234" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Código de Barras</label>
                                <input type="text" x-model="form.codigo_barras" placeholder="7501234567890" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" x-model="form.nombre" placeholder="Taladro Percutor 800W" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Descripción</label>
                                <textarea x-model="form.descripcion" rows="3" placeholder="Descripción detallada..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Categorización --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">label</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Categorización</h3>
                        </div>
                        <div class="p-5 grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Categoría <span class="text-danger">*</span></label>
                                <select x-model="form.categoria" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="">Seleccionar...</option>
                                    <option>Herramientas Eléctricas</option><option>Herramientas Manuales</option>
                                    <option>Equipo de Seguridad</option><option>Material Eléctrico</option>
                                    <option>Fontanería</option><option>Pinturas y Recubrimientos</option>
                                    <option>Ferretería General</option><option>Construcción</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Marca</label>
                                <select x-model="form.marca" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="">Seleccionar...</option>
                                    <option>Bosch</option><option>DeWalt</option><option>Makita</option>
                                    <option>Milwaukee</option><option>Stanley</option><option>Black+Decker</option>
                                    <option>Truper</option><option>Urrea</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Unidad de Medida</label>
                                <select x-model="form.unidad_medida" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option>Pieza</option><option>Caja</option><option>Paquete</option>
                                    <option>Metro</option><option>Litro</option><option>Kilogramo</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Ubicación en Almacén</label>
                                <input type="text" x-model="form.ubicacion" placeholder="A-01-03" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                        </div>
                    </div>

                    {{-- Proveedores --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">local_shipping</span>
                                <h3 class="font-black text-gray-900 dark:text-white text-sm">Proveedores</h3>
                            </div>
                            <button type="button" @click="addProveedor" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-brand text-white hover:bg-primary-700 transition-all">
                                <span class="material-symbols-outlined text-[14px]">add</span>Agregar
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="bg-gray-50 dark:bg-white/[0.03]">
                                    <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Proveedor</th>
                                    <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">SKU Proveedor</th>
                                    <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Precio</th>
                                    <th class="px-4 py-3 text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Entrega</th>
                                    <th class="px-4 py-3" style="width:40px"></th>
                                </tr></thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                                    <template x-for="(p,i) in form.proveedores" :key="i">
                                        <tr>
                                            <td class="px-4 py-3">
                                                <select x-model="p.proveedor_id" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                                    <option value="">Seleccionar...</option>
                                                    <option value="1">Grupo Ferretero SA</option>
                                                    <option value="2">Importaciones Martínez</option>
                                                    <option value="3">Distribuidora Industrial</option>
                                                    <option value="4">Proveedores Unidos</option>
                                                    <option value="5">Suministros Globales</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-3"><input type="text" x-model="p.sku_proveedor" placeholder="SKU" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></td>
                                            <td class="px-4 py-3"><div class="flex items-center gap-1"><span class="text-gray-400 text-sm">$</span><input type="number" x-model="p.precio_compra" step="0.01" min="0" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-right focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></div></td>
                                            <td class="px-4 py-3"><div class="flex items-center gap-1"><input type="number" x-model="p.tiempo_entrega" placeholder="días" min="1" class="w-20 px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"><span class="text-xs text-gray-400">días</span></div></td>
                                            <td class="px-4 py-3 text-center"><button type="button" @click="removeProveedor(i)" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-danger/10 hover:text-danger transition-all"><span class="material-symbols-outlined text-[16px]">close</span></button></td>
                                        </tr>
                                    </template>
                                    <tr x-show="form.proveedores.length===0">
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-600 text-sm">
                                            <span class="material-symbols-outlined text-3xl block mb-1">local_shipping</span>Sin proveedores
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR --}}
                <div class="space-y-5">

                    {{-- Precios --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">attach_money</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Precios y Stock</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Precio de Compra</label>
                                <div class="flex items-center gap-1"><span class="text-gray-400 text-sm">$</span><input type="number" x-model="form.precio_compra" step="0.01" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-right focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Precio de Venta <span class="text-danger">*</span></label>
                                <div class="flex items-center gap-1"><span class="text-gray-400 text-sm">$</span><input type="number" x-model="form.precio_venta" step="0.01" min="0" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-right focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></div>
                            </div>
                            <div class="p-3 bg-success/10 rounded-xl text-center">
                                <p class="text-lg font-black text-gray-900 dark:text-white" x-text="formatCurrency(utilidad)"></p>
                                <p class="text-xs text-gray-400 dark:text-gray-500" x-text="utilidadPct + '% utilidad estimada'"></p>
                            </div>
                            <hr class="border-gray-100 dark:border-white/[0.06]">
                            <div class="grid grid-cols-3 gap-3">
                                <div><label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Stock Ini.</label><input type="number" x-model="form.stock_inicial" min="0" step="1" class="w-full px-2.5 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></div>
                                <div><label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Mínimo</label><input type="number" x-model="form.stock_minimo" min="0" step="1" class="w-full px-2.5 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></div>
                                <div><label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Máximo</label><input type="number" x-model="form.stock_maximo" min="0" step="1" class="w-full px-2.5 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] p-5 space-y-2.5">
                        <button type="submit" :disabled="!isValid" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-brand text-white hover:bg-primary-700 transition-all shadow-sm shadow-brand/20 disabled:opacity-50">
                            <span class="material-symbols-outlined text-[18px]">save</span>Guardar Producto
                        </button>
                        <button type="button" @click="resetForm" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/[0.10] text-gray-600 dark:text-gray-400 hover:border-danger hover:text-danger transition-all">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>Limpiar
                        </button>
                    </div>

                    {{-- Ejemplo --}}
                    <div class="bg-warning/10 border border-warning/20 rounded-2xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-warning text-[18px]">lightbulb</span>
                            <p class="text-sm font-bold text-warning">Ejemplo Rápido</p>
                        </div>
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
function productoForm() {
    return {
        form:{ sku:'',codigo_barras:'',nombre:'',descripcion:'',categoria:'',marca:'',unidad_medida:'Pieza',ubicacion:'',precio_compra:0,precio_venta:0,stock_inicial:0,stock_minimo:5,stock_maximo:100,iva:16,proveedores:[] },
        init(){},
        addProveedor(){ this.form.proveedores.push({proveedor_id:'',sku_proveedor:'',precio_compra:0,tiempo_entrega:7}); },
        removeProveedor(i){ this.form.proveedores.splice(i,1); },
        get utilidad(){ return (parseFloat(this.form.precio_venta)||0)-(parseFloat(this.form.precio_compra)||0); },
        get utilidadPct(){ return this.form.precio_compra>0?((this.utilidad/this.form.precio_compra)*100).toFixed(1):0; },
        get isValid(){ return this.form.sku&&this.form.nombre&&this.form.categoria&&this.form.precio_venta>0; },
        formatCurrency(v){ return new Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN',minimumFractionDigits:2}).format(v||0); },
        loadExample(){
            this.form={sku:'SKU-BOS-1234',codigo_barras:'7501234567890',nombre:'Taladro Percutor 800W Professional',descripcion:'Taladro percutor con velocidad variable, mandril 1/2", maletín incluido.',categoria:'Herramientas Eléctricas',marca:'Bosch',unidad_medida:'Pieza',ubicacion:'A-01-03',precio_compra:1250.50,precio_venta:1899.99,stock_inicial:25,stock_minimo:5,stock_maximo:50,iva:16,proveedores:[{proveedor_id:'1',sku_proveedor:'BOS-TAL-800',precio_compra:1250.50,tiempo_entrega:5}]};
            Swal.fire({title:'✅ Ejemplo cargado',icon:'success',timer:1500,showConfirmButton:false});
        },
        resetForm(){ if(confirm('¿Limpiar formulario?')) this.form={sku:'',codigo_barras:'',nombre:'',descripcion:'',categoria:'',marca:'',unidad_medida:'Pieza',ubicacion:'',precio_compra:0,precio_venta:0,stock_inicial:0,stock_minimo:5,stock_maximo:100,iva:16,proveedores:[]}; },
        saveProducto(){ if(!this.isValid){ Swal.fire({title:'Campos requeridos',icon:'warning'}); return; } Swal.fire({title:'✅ Producto guardado',html:`SKU: <strong>${this.form.sku}</strong><br>Precio: <strong>${this.formatCurrency(this.form.precio_venta)}</strong><br><small class="text-gray-400">(Modo Demo)</small>`,icon:'success'}).then(()=>{ if(confirm('¿Ver inventario?')) window.location.href='{{ route("demo.inventario") }}'; }); },
    };
}
</script>
@endpush
