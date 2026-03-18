{{-- file: resources/views/demos/clientes/create.blade.php --}}
@extends('layouts.demo')
@section('title', 'Nuevo Cliente — Demo')

@section('content')
    <a href="{{ route('demo.clientes') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-brand transition-colors mb-5">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>Volver a Clientes
    </a>
    <div x-data="{ open:true }" x-show="open" class="flex items-start justify-between gap-3 p-4 rounded-xl bg-info/10 border border-info/20 text-info mb-5">
        <div class="flex items-start gap-2.5"><span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">info</span>
            <p class="text-sm font-medium"><strong>Modo Demo:</strong> Esta es una interfaz de demostración. Los datos no se guardarán.</p></div>
        <button @click="open=false" class="shrink-0 text-info/60 hover:text-info"><span class="material-symbols-outlined text-[18px]">close</span></button>
    </div>
    <div x-data="clienteForm()">
        <form @submit.prevent="saveCliente">
            <div class="grid lg:grid-cols-3 gap-5">

                {{-- ── MAIN ────────────────────────────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Datos básicos --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">person</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Datos Básicos</h3>
                        </div>
                        <div class="p-5 grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Nombre / Razón Social <span class="text-danger">*</span></label>
                                <input type="text" x-model="form.nombre" placeholder="Juan García / Empresa SA" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">RFC <span class="text-danger">*</span></label>
                                <input type="text" x-model="form.rfc" @input="form.rfc=form.rfc.toUpperCase()" placeholder="XAXX010101000" maxlength="13" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm uppercase font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                <p class="text-xs text-gray-400 mt-1">12 dígitos persona física, 13 moral. 'XAXX010101000' para público general.</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Tipo</label>
                                <div class="flex gap-2">
                                    <template x-for="t in ['Empresa','Persona física','Gobierno']" :key="t">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" x-model="form.tipo" :value="t" class="sr-only">
                                            <div :class="form.tipo===t?'bg-brand/10 border-brand text-brand':'border-gray-200 dark:border-white/[0.10] text-gray-500 hover:border-brand hover:text-brand'"
                                                 class="flex items-center justify-center px-2 py-2 rounded-xl border text-xs font-bold transition-all text-center">
                                                <span x-text="t"></span>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Giro / Industria</label>
                                <select x-model="form.giro" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="">Seleccionar...</option>
                                    @foreach(['Construcción','Manufactura','Logística','Agroindustria','Minería','Energía','Tecnología','Comercio','Servicios','Gobierno'] as $g)
                                    <option value="{{ $g }}">{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" x-model="form.email" placeholder="contacto@empresa.com" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Teléfono</label>
                                <input type="tel" x-model="form.telefono" placeholder="33 1234 5678" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Sitio Web</label>
                                <input type="url" x-model="form.web" placeholder="https://empresa.com" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Estado</label>
                                <select x-model="form.status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                    <option value="prospecto">Prospecto</option>
                                    <option value="bloqueado">Bloqueado</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Notas</label>
                                <textarea x-model="form.notas" rows="2" placeholder="Notas internas..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Dirección --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">location_on</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Dirección</h3>
                        </div>
                        <div class="p-5 grid sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Calle y número</label>
                                <input type="text" x-model="form.direccion.calle" placeholder="Av. Principal 123" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Colonia</label>
                                <input type="text" x-model="form.direccion.colonia" placeholder="Colonia Centro" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Código Postal</label>
                                <input type="text" x-model="form.direccion.cp" placeholder="45000" maxlength="5" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Ciudad</label>
                                <input type="text" x-model="form.direccion.ciudad" placeholder="Guadalajara" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Estado / Municipio</label>
                                <select x-model="form.direccion.estado" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="">Seleccionar...</option>
                                    @foreach(['Jalisco','CDMX','Nuevo León','Guanajuato','Coahuila','Michoacán','Estado de México','Veracruz','Sonora','Chihuahua'] as $e)
                                    <option>{{ $e }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Contactos --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">contacts</span>
                                <h3 class="font-black text-gray-900 dark:text-white text-sm">Contactos</h3>
                            </div>
                            <button type="button" @click="addContacto" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-brand text-white hover:bg-primary-700 transition-all">
                                <span class="material-symbols-outlined text-[14px]">add</span>Agregar
                            </button>
                        </div>
                        <div class="p-5">
                            <div class="space-y-3">
                                <template x-for="(c,i) in form.contactos" :key="i">
                                    <div class="grid sm:grid-cols-4 gap-3 p-3 bg-gray-50 dark:bg-white/[0.03] rounded-xl">
                                        <input type="text" x-model="c.nombre" placeholder="Nombre" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                        <input type="email" x-model="c.email" placeholder="email@..." class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                        <input type="tel" x-model="c.telefono" placeholder="Teléfono" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                        <div class="flex items-center gap-2">
                                            <select x-model="c.puesto" class="flex-1 px-3 py-2 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                                <option value="">Puesto...</option>
                                                <option>Compras</option><option>Dirección</option>
                                                <option>Contabilidad</option><option>Operaciones</option>
                                            </select>
                                            <button type="button" @click="removeContacto(i)" class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:bg-danger/10 hover:text-danger transition-all shrink-0"><span class="material-symbols-outlined text-[16px]">close</span></button>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="form.contactos.length===0" class="py-8 text-center text-gray-400 text-sm">
                                    <span class="material-symbols-outlined text-3xl block mb-1">contacts</span>Sin contactos. Agrega uno.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── SIDEBAR ──────────────────────────────────────────────────── --}}
                <div class="space-y-5">

                    {{-- Condiciones comerciales --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07]">
                        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-white/[0.07]">
                            <span class="material-symbols-outlined text-brand text-[20px]" style="font-variation-settings:'FILL' 0,'wght' 300">handshake</span>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">Condiciones</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Límite de crédito</label>
                                <div class="flex items-center gap-1"><span class="text-gray-400 text-sm">$</span><input type="number" x-model="form.limite_credito" min="0" step="1000" placeholder="0" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm text-right focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors"></div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Días de crédito</label>
                                <select x-model="form.dias_credito" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option value="0">Contado</option>
                                    <option value="8">8 días</option><option value="15">15 días</option>
                                    <option value="30">30 días</option><option value="45">45 días</option>
                                    <option value="60">60 días</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Descuento general</label>
                                <div class="flex items-center gap-1.5"><input type="range" x-model="form.descuento" min="0" max="30" step="1" class="flex-1"><span class="text-sm font-bold text-brand w-12 text-right" x-text="form.descuento+'%'"></span></div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Vendedor Asignado</label>
                                <select x-model="form.vendedor" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.10] bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-colors">
                                    <option>Carlos Rodríguez</option><option>María Pérez</option>
                                    <option>Juan Vásquez</option><option>Ana González</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] p-5 space-y-2.5">
                        <button type="submit" :disabled="!isValid" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-brand text-white hover:bg-primary-700 transition-all shadow-sm shadow-brand/20 disabled:opacity-50">
                            <span class="material-symbols-outlined text-[18px]">save</span>Guardar Cliente
                        </button>
                        <button type="button" @click="resetForm" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/[0.10] text-gray-600 dark:text-gray-400 hover:border-danger hover:text-danger transition-all">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>Limpiar
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
function clienteForm() {
    return {
        form:{nombre:'',rfc:'',tipo:'Empresa',giro:'',email:'',telefono:'',web:'',status:'activo',notas:'',limite_credito:0,dias_credito:30,descuento:0,vendedor:'Carlos Rodríguez',direccion:{calle:'',colonia:'',cp:'',ciudad:'',estado:''},contactos:[]},
        addContacto(){this.form.contactos.push({nombre:'',email:'',telefono:'',puesto:'Compras'});},
        removeContacto(i){this.form.contactos.splice(i,1);},
        get isValid(){return this.form.nombre&&this.form.rfc&&this.form.email;},
        loadExample(){this.form={nombre:'Constructora del Bajío SA de CV',rfc:'CBB991015XYZ',tipo:'Empresa',giro:'Construcción',email:'contacto@constructorabajio.com',telefono:'33 1234 5678',web:'https://constructorabajio.com',status:'activo',notas:'Cliente premium. Requiere crédito a 30 días.',limite_credito:500000,dias_credito:30,descuento:5,vendedor:'Carlos Rodríguez',direccion:{calle:'Av. Vallarta 3200 Piso 4',colonia:'Vallarta San Jorge',cp:'44690',ciudad:'Guadalajara',estado:'Jalisco'},contactos:[{nombre:'Roberto Campos',email:'rcampos@constructorabajio.com',telefono:'33 9876 5432',puesto:'Compras'},{nombre:'Ing. Patricia Ruiz',email:'pruiz@constructorabajio.com',telefono:'33 1111 2222',puesto:'Dirección'}]};Swal.fire({title:'✅ Ejemplo cargado',icon:'success',timer:1500,showConfirmButton:false});},
        resetForm(){if(confirm('¿Limpiar formulario?'))this.form={nombre:'',rfc:'',tipo:'Empresa',giro:'',email:'',telefono:'',web:'',status:'activo',notas:'',limite_credito:0,dias_credito:30,descuento:0,vendedor:'Carlos Rodríguez',direccion:{calle:'',colonia:'',cp:'',ciudad:'',estado:''},contactos:[]};},
        saveCliente(){if(!this.isValid){Swal.fire({title:'Campos requeridos',text:'Nombre, RFC y email son obligatorios.',icon:'warning'});return;}Swal.fire({title:'✅ Cliente guardado',html:`<strong>${this.form.nombre}</strong><br>RFC: ${this.form.rfc}<br><small class="text-gray-400">(Modo Demo)</small>`,icon:'success'}).then(()=>{if(confirm('¿Ver listado?'))window.location.href='{{ route("demo.clientes") }}';});},
    };
}
</script>
@endpush
