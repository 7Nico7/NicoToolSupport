{{-- file: resources/views/public/servicios/desarrollo.blade.php --}}
@extends('layouts.public')
@section('title', 'Desarrollo Web a Medida — JVJ Technology')
@section('meta_description', 'Creamos sistemas web empresariales, ERPs, portales y plataformas digitales personalizadas. Analizamos tu operación y construimos lo que realmente necesitas.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">storage</span>
                Servicio
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Desarrollo<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">Web a Medida</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Sistemas web empresariales, portales, ERPs y plataformas digitales completamente personalizadas para tu negocio.
            </p>
        </div>
    </div>
</section>

{{-- QUÉ HACEMOS --}}
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <span class="inline-block text-brand dark:text-primary-400 text-xs font-black uppercase tracking-widest mb-3">Por qué elegirnos</span>
                <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                    Sistemas web que<br>impulsan tu negocio
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-[15px] leading-relaxed mb-8">
                    Desarrollamos aplicaciones web robustas, escalables y seguras, adaptadas exactamente a tus procesos.
                    <strong class="text-gray-900 dark:text-white font-semibold">No vendemos software genérico:</strong>
                    analizamos tu operación y construimos lo que realmente necesitas.
                </p>
                <div class="space-y-4">
                    @foreach([
                        ['icon'=>'architecture',  'title'=>'Arquitectura robusta',         'desc'=>'Sistemas preparados para crecer con tu negocio'],
                        ['icon'=>'lock',          'title'=>'Seguridad empresarial',         'desc'=>'Protección de datos y roles de usuario por perfil'],
                        ['icon'=>'public',        'title'=>'Acceso desde cualquier navegador','desc'=>'Sin instalaciones, siempre actualizado automáticamente'],
                        ['icon'=>'integration_instructions','title'=>'API e integraciones','desc'=>'Conéctate con cualquier sistema o plataforma existente'],
                    ] as $feat)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 bg-primary-50 dark:bg-primary-950/60 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-brand dark:text-primary-400 text-[17px]"
                                  style="font-variation-settings:'FILL' 0,'wght' 300">{{ $feat['icon'] }}</span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $feat['title'] }}</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">{{ $feat['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div data-aos="fade-left">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://placehold.co/600x420/1e3a8a/ffffff?text=Desarrollo+Web"
                         alt="Desarrollo Web JVJ" class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-950/40 to-transparent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TIPOS DE APLICACIONES --}}
<section class="py-20 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Qué construimos
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">
                Tipos de aplicaciones web
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Desde pequeños módulos hasta sistemas empresariales completos
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['icon'=>'widgets',       'title'=>'Sistemas ERP',              'desc'=>'Control total: ventas, compras, inventario, contabilidad, RRHH y más en un mismo sistema.'],
                ['icon'=>'storefront',    'title'=>'E-commerce y tiendas',      'desc'=>'Catálogos, carritos, pasarelas de pago y panel administrativo integrado.'],
                ['icon'=>'donut_large',   'title'=>'Dashboards y reportes',     'desc'=>'Visualización de datos en tiempo real, KPIs y reportes personalizados.'],
                ['icon'=>'group',         'title'=>'CRM',                       'desc'=>'Gestión de clientes, historial de interacciones, seguimiento de ventas y automatización.'],
                ['icon'=>'task_alt',      'title'=>'Gestión de proyectos',      'desc'=>'Plataformas para administrar tareas, equipos, tiempos y entregables.'],
                ['icon'=>'receipt_long',  'title'=>'Facturación electrónica',   'desc'=>'Módulos de facturación con timbrado, cancelación y seguimiento fiscal.'],
            ] as $i => $app)
            <div class="group bg-background-light dark:bg-background-dark rounded-2xl p-6
                        border border-gray-100 dark:border-white/[0.07]
                        hover:border-brand dark:hover:border-brand hover:shadow-lg hover:-translate-y-1
                        transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">
                <div class="w-12 h-12 bg-primary-50 dark:bg-primary-950/60 group-hover:bg-brand rounded-xl
                            flex items-center justify-center mb-4 transition-colors duration-300">
                    <span class="material-symbols-outlined text-brand dark:text-primary-400 group-hover:text-white text-[22px] transition-colors duration-300"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $app['icon'] }}</span>
                </div>
                <h3 class="font-black text-gray-900 dark:text-white mb-2 group-hover:text-brand dark:group-hover:text-primary-400 transition-colors">
                    {{ $app['title'] }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">{{ $app['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- TECNOLOGÍAS --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-12" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Stack
            </span>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-3">Tecnologías que dominamos</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                Elegimos el stack que mejor se adapta a cada proyecto
            </p>
        </div>
        <div class="flex flex-wrap justify-center gap-3" data-aos="fade-up">
            @foreach(['Phalcon','Laravel','Django','React','Vue.js','MySQL','PostgreSQL','MongoDB'] as $tech)
            <span class="px-5 py-2.5 rounded-xl text-sm font-bold
                         bg-surface-light dark:bg-surface-dark
                         text-gray-700 dark:text-gray-300
                         border border-gray-200 dark:border-white/[0.10]
                         hover:border-brand dark:hover:border-primary-500 hover:text-brand dark:hover:text-primary-400
                         transition-all duration-200">{{ $tech }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- CROSSLINK MÓVIL --}}
<section class="py-16 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center" data-aos="fade-up">
        <div class="w-14 h-14 bg-primary-50 dark:bg-primary-950/60 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <span class="material-symbols-outlined text-brand dark:text-primary-400 text-[28px]"
                  style="font-variation-settings:'FILL' 0,'wght' 300">link</span>
        </div>
        <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-4">¿Necesitas también una app móvil?</h2>
        <p class="text-gray-500 dark:text-gray-400 text-[15px] leading-relaxed mb-8">
            Tu sistema web puede extenderse al móvil. Desarrollamos apps nativas e híbridas que se sincronizan en tiempo real con tu plataforma, permitiendo capturar evidencias, consultar reportes o gestionar todo desde cualquier dispositivo.
        </p>
        <a href="{{ route('public.servicios.movil') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold
                  bg-background-light dark:bg-background-dark border border-gray-200 dark:border-white/[0.10]
                  text-gray-700 dark:text-gray-300 hover:border-brand hover:text-brand dark:hover:border-primary-400 dark:hover:text-primary-400
                  transition-all duration-200">
            <span class="material-symbols-outlined text-[18px]">smartphone</span>
            Conoce desarrollo móvil
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-15"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-10"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-black mb-4">¿Necesitas un sistema web a medida?</h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Cuéntanos qué necesitas y te prepararemos una propuesta sin compromiso.
        </p>
        <a href="{{ route('public.contacto') }}"
           class="group inline-flex items-center gap-2 px-8 py-4 bg-brand text-white font-black rounded-xl
                  hover:bg-primary-600 active:bg-primary-700 transition-all duration-200
                  shadow-xl shadow-brand/30 hover:shadow-brand/50 hover:scale-[1.02]">
            <span class="material-symbols-outlined text-[20px]">send</span>
            Solicitar cotización
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>
    </div>
</section>

@endsection
