@extends('layouts.public')
@section('title', 'Portafolio de Proyectos — JVJ Technology')
@section('meta_description', 'Proyectos desarrollados por JVJ Technology: ERP, apps móviles, e-commerce y sistemas web para empresas en México y Latinoamérica.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')">
    </div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-24 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20
                         text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">work</span>
                Nuestro trabajo
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Portafolio<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">de Proyectos</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Algunos de los proyectos que hemos construido para empresas reales.
            </p>
        </div>
    </div>
</section>

{{-- FILTROS --}}
<div class="sticky top-14 z-20 bg-surface-light/95 dark:bg-surface-dark/95 backdrop-blur-md
            border-b border-gray-200 dark:border-white/[0.07] py-4"
     x-data="{ active: 'todos' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-2">
            @foreach([
                ['key'=>'todos',     'label'=>'Todos'],
                ['key'=>'erp',       'label'=>'ERP'],
                ['key'=>'movil',     'label'=>'Apps Móviles'],
                ['key'=>'ecommerce', 'label'=>'E-commerce'],
                ['key'=>'web',       'label'=>'Sistemas Web'],
            ] as $f)
            <button
                @click="active = '{{ $f['key'] }}'"
                :class="active === '{{ $f['key'] }}'
                    ? 'bg-brand text-white shadow-sm shadow-brand/20'
                    : 'bg-background-light dark:bg-background-dark text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-white/[0.10] hover:border-brand hover:text-brand dark:hover:text-primary-400'"
                class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-200">
                {{ $f['label'] }}
            </button>
            @endforeach
        </div>
    </div>
</div>

{{-- PROYECTOS --}}
<section class="py-16 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                [
                    'color'=>'1e3a8a','label'=>'ERP+Logística','alt'=>'ERP Logística',
                    'cat'=>'ERP','cat_tw'=>'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                    'title'=>'Sistema ERP para Logística',
                    'desc'=>'ERP completo con control de flota, rutas y seguimiento de entregas en tiempo real.',
                    'year'=>'2023','city'=>'CDMX, México',
                ],
                [
                    'color'=>'064e3b','label'=>'App+Ventas','alt'=>'App Ventas',
                    'cat'=>'App Móvil','cat_tw'=>'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400',
                    'title'=>'App de Ventas en Campo',
                    'desc'=>'Aplicación móvil para que vendedores tomen pedidos en ruta con sincronización en tiempo real.',
                    'year'=>'2024','city'=>'Monterrey, México',
                ],
                [
                    'color'=>'78350f','label'=>'ERP+Retail','alt'=>'ERP Retail',
                    'cat'=>'ERP','cat_tw'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
                    'title'=>'ERP para Cadena de Tiendas',
                    'desc'=>'Sistema centralizado para 15 tiendas: inventario, ventas y facturación unificados.',
                    'year'=>'2023','city'=>'Guadalajara, México',
                ],
                [
                    'color'=>'3b0764','label'=>'App+Inventario','alt'=>'App Inventario',
                    'cat'=>'App Móvil','cat_tw'=>'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-400',
                    'title'=>'App de Inventario con Código de Barras',
                    'desc'=>'Gestión de inventario mediante lectura de códigos de barras y QR con modo offline.',
                    'year'=>'2024','city'=>'Puebla, México',
                ],
                [
                    'color'=>'7f1d1d','label'=>'E-commerce','alt'=>'E-commerce',
                    'cat'=>'E-commerce','cat_tw'=>'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                    'title'=>'Tienda Online + ERP Integrado',
                    'desc'=>'Tienda en línea con sincronización automática de inventario y pedidos hacia el ERP.',
                    'year'=>'2023','city'=>'Querétaro, México',
                ],
                [
                    'color'=>'172554','label'=>'ERP+Manufactura','alt'=>'ERP Manufactura',
                    'cat'=>'ERP','cat_tw'=>'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                    'title'=>'ERP para Planta de Manufactura',
                    'desc'=>'Planificación de producción, control de materia prima y órdenes de trabajo.',
                    'year'=>'2022','city'=>'Tijuana, México',
                ],
            ] as $i => $p)
            <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl overflow-hidden
                        border border-gray-100 dark:border-white/[0.07]
                        hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">

                {{-- Imagen --}}
                <div class="relative h-48 overflow-hidden">
                    <img src="https://placehold.co/600x320/{{ $p['color'] }}/ffffff?text={{ $p['label'] }}"
                         alt="{{ $p['alt'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <span class="absolute top-3 left-3 text-[11px] font-bold uppercase tracking-wide
                                 px-2.5 py-1 rounded-full backdrop-blur-sm bg-black/30 border border-white/20 text-white">
                        {{ $p['cat'] }}
                    </span>
                </div>

                {{-- Contenido --}}
                <div class="p-6">
                    <h3 class="font-black text-gray-900 dark:text-white text-base leading-snug mb-2
                               group-hover:text-brand dark:group-hover:text-primary-400 transition-colors">
                        {{ $p['title'] }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-4">
                        {{ $p['desc'] }}
                    </p>
                    <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500 pt-4
                                border-t border-gray-100 dark:border-white/[0.06]">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                            {{ $p['year'] }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[13px]">location_on</span>
                            {{ $p['city'] }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="flex justify-center mt-12 gap-1.5" data-aos="fade-up">
            @foreach(range(1, 3) as $page)
            <a href="#"
               class="w-9 h-9 flex items-center justify-center rounded-xl text-sm font-bold transition-all duration-200
                      {{ $page === 1
                          ? 'bg-brand text-white shadow-sm shadow-brand/25'
                          : 'bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/[0.10] text-gray-600 dark:text-gray-400 hover:border-brand hover:text-brand dark:hover:text-primary-400' }}">
                {{ $page }}
            </a>
            @endforeach
            <span class="w-9 h-9 flex items-center justify-center text-gray-400 dark:text-gray-500 text-sm">…</span>
            <a href="#"
               class="w-9 h-9 flex items-center justify-center rounded-xl text-sm font-bold
                      bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/[0.10]
                      text-gray-600 dark:text-gray-400 hover:border-brand hover:text-brand dark:hover:text-primary-400
                      transition-all duration-200">
                10
            </a>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.15]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.10]"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-black mb-4">¿Quieres un proyecto similar?</h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Hablemos y construyamos la solución perfecta para tu negocio.
        </p>
        <a href="{{ route('public.contacto') }}"
           class="group inline-flex items-center gap-2 px-8 py-4 bg-brand text-white font-black rounded-xl
                  hover:bg-primary-600 active:bg-primary-700 transition-all duration-200
                  shadow-xl shadow-brand/30 hover:shadow-brand/50 hover:scale-[1.02]">
            <span class="material-symbols-outlined text-[20px]">rocket_launch</span>
            Iniciar proyecto
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>
    </div>
</section>

@endsection
