@extends('layouts.demo')

@section('title', 'Nuevo Movimiento — Demo')

@section('content')

    {{-- Header de Acción --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('demo.inventario.existencia') }}"
            class="group inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-brand transition-all">
            <span class="material-symbols-outlined text-[18px] transition-transform group-hover:-translate-x-1">arrow_back</span>
            Volver a Movimientos
        </a>

        <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-tighter">
            ID Transacción: <span class="text-brand">#{{ strtoupper(uniqid()) }}</span>
        </div>
    </div>

    {{-- Aviso demo (Componentizable) --}}
    <div x-data="{ open: true }" x-show="open" x-transition
        class="flex items-center justify-between gap-3 p-4 rounded-2xl bg-brand/5 border border-brand/10 text-brand mb-6 backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-brand/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">verified</span>
            </div>
            <p class="text-xs font-bold uppercase tracking-tight">
                <strong>Modo Demo:</strong> Estás en un entorno de pruebas seguro.
            </p>
        </div>
        <button @click="open=false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-brand/10 transition-colors">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>

    <div x-data="movimientoForm()">
        <form @submit.prevent="saveMovimiento">
            <div class="grid lg:grid-cols-3 gap-6">

                {{-- ================= MAIN COLUMN ================= --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Card: Configuración --}}
                    <div class="bg-surface border border-gray-200 dark:border-white/[0.08] rounded-3xl overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.05] bg-gray-50/50 dark:bg-white/[0.01]">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand">tune</span>
                                <h3 class="font-black text-gray-900 dark:text-white text-xs uppercase tracking-widest">Configuración del Movimiento</h3>
                            </div>
                        </div>

                        <div class="p-6 grid sm:grid-cols-2 gap-6">
                            {{-- Selector de Tipo con Radio Cards --}}
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">Naturaleza de la Operación</label>
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach(['entrada' => ['south', 'success'], 'salida' => ['north', 'danger'], 'ajuste' => ['balance', 'warning']] as $val => $meta)
                                    <label class="cursor-pointer group">
                                        <input type="radio" x-model="form.tipo" value="{{ $val }}" class="sr-only">
                                        <div :class="form.tipo === '{{ $val }}' ? 'border-{{ $meta[1] }} bg-{{ $meta[1] }}/5 text-{{ $meta[1] }}' : 'border-gray-200 dark:border-white/[0.08] text-gray-400'"
                                             class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all group-hover:border-{{ $meta[1] }}/50">
                                            <span class="material-symbols-outlined text-[24px]">{{ $meta[0] }}</span>
                                            <span class="text-[10px] font-black uppercase">{{ ucfirst($val) }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Fecha --}}
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Fecha de Registro</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400 text-[20px]">calendar_today</span>
                                    <input type="datetime-local" x-model="form.fecha"
                                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 dark:border-white/[0.1] bg-gray-50 dark:bg-white/[0.03] text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card: Producto --}}
                    <div class="bg-surface border border-gray-200 dark:border-white/[0.08] rounded-3xl overflow-hidden shadow-sm">
                        <div class="p-6 grid sm:grid-cols-3 gap-6">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Producto / SKU</label>
                                <select x-model="form.producto_id"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/[0.1] bg-gray-50 dark:bg-white/[0.03] text-sm font-bold focus:border-brand outline-none transition-all">
                                    <option value="">Seleccionar producto...</option>
                                    <template x-for="(prod, id) in productos" :key="id">
                                        <option :value="id" x-text="prod.nombre"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="relative group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-center">Stock Actual</label>
                                <div class="flex items-center justify-center h-[48px] bg-brand/5 dark:bg-brand/10 rounded-xl border-2 border-dashed border-brand/20 overflow-hidden relative">
                                    <span class="text-2xl font-black text-brand" x-text="stockActual"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= SIDEBAR ================= --}}
                <div class="space-y-6">
                    {{-- Card: Resumen dinámico --}}
                    <div class="bg-surface border border-gray-200 dark:border-white/[0.08] rounded-3xl p-6 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <span class="material-symbols-outlined text-[64px]">analytics</span>
                        </div>

                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Vista Previa</h3>

                        <div class="space-y-4 relative">
                            <div class="flex justify-between items-end">
                                <span class="text-sm text-gray-500 font-medium">Estado Final</span>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 uppercase font-black leading-none">Stock Calculado</p>
                                    <p class="text-3xl font-black text-gray-900 dark:text-white" x-text="stockFinal"></p>
                                </div>
                            </div>

                            <div class="h-1.5 w-full bg-gray-100 dark:bg-white/[0.05] rounded-full overflow-hidden">
                                <div class="h-full bg-brand transition-all duration-500" :style="`width: ${Math.min(stockFinal * 2, 100)}%` text-brand"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="space-y-3">
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand text-white font-black uppercase text-[11px] tracking-widest hover:bg-brand/90 transition-all shadow-lg shadow-brand/20 active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            Confirmar Registro
                        </button>

                        <button type="button" @click="resetForm"
                            class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl border border-gray-200 dark:border-white/[0.1] text-gray-500 font-black uppercase text-[11px] tracking-widest hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-all">
                            <span class="material-symbols-outlined text-[20px]">history</span>
                            Restablecer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
