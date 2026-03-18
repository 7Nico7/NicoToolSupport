@extends('layouts.public')

@section('title', 'JVJ Technology — Desarrollo de ERP y Apps Móviles Empresariales')
@section('meta_description', 'Desarrollamos ERP a medida y apps móviles conectadas. Automatiza tu facturación, inventario y procesos con soluciones tecnológicas personalizadas para tu empresa.')

@section('styles')
<style>
    @keyframes blob {
        0%   { transform: translate(0,0) scale(1); }
        33%  { transform: translate(30px,-50px) scale(1.1); }
        66%  { transform: translate(-20px,20px) scale(0.9); }
        100% { transform: translate(0,0) scale(1); }
    }
    @keyframes float {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-10px); }
    }
    @keyframes bar-grow {
        from { transform: scaleY(0); }
        to   { transform: scaleY(1); }
    }
    .animate-blob     { animation: blob 7s infinite; }
    .animate-float    { animation: float 3s ease-in-out infinite; }
    .delay-2000       { animation-delay: 2s; }
    .delay-4000       { animation-delay: 4s; }
    .bar-anim         { transform-origin: bottom; animation: bar-grow 1s ease-out forwards; }

    /* Card shine on hover */
    .card-shine::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,.06) 50%, transparent 60%);
        opacity: 0;
        transition: opacity .3s;
        pointer-events: none;
        border-radius: inherit;
    }
    .card-shine:hover::before { opacity: 1; }
</style>
@endsection

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white pt-24 pb-20 md:pt-32 md:pb-28 min-h-[88vh] flex items-center">

    {{-- Fondo: grid sutil --}}
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')">
    </div>

    {{-- Blobs de color --}}
    <div class="absolute -top-40 -right-40 w-80 h-80 bg-brand rounded-full blur-3xl opacity-[0.18] animate-blob"></div>
    <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary-700 rounded-full blur-3xl opacity-[0.15] animate-blob delay-2000"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-primary-800 rounded-full blur-3xl opacity-10 animate-blob delay-4000"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- ── Columna izquierda ─────────────────────────────────── --}}
            <div data-aos="fade-right" data-aos-duration="700">

                {{-- Badge --}}
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-200 text-xs font-semibold uppercase tracking-widest mb-6">
                    <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                    Soluciones a medida para tu empresa
                </span>

                {{-- Título --}}
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight mb-6">
                    <span class="block text-white">Desarrollo de</span>
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">ERP y Apps</span>
                    <span class="block text-white">Empresariales</span>
                </h1>

                {{-- Descripción --}}
                <p class="text-lg text-primary-200/80 leading-relaxed mb-8 max-w-lg">
                    Automatizamos tu <strong class="text-white font-semibold">facturación, inventario y procesos</strong> con sistemas completamente personalizados.
                    Nos sentamos contigo antes de escribir una sola línea de código.
                </p>

                {{-- Pills de beneficios --}}
                <div class="flex flex-wrap gap-2 mb-10">
                    @foreach([
                        ['icon'=>'schedule',   'label'=>'Soporte continuo'],
                        ['icon'=>'security',   'label'=>'100% seguro'],
                        ['icon'=>'smartphone', 'label'=>'Multiplataforma'],
                        ['icon'=>'cloud',      'label'=>'En la nube'],
                    ] as $pill)
                    <span class="inline-flex items-center gap-1.5 bg-white/[0.08] hover:bg-white/[0.14] border border-white/10 px-3 py-1.5 rounded-lg text-xs font-medium text-primary-200 transition-colors duration-200 cursor-default">
                        <span class="material-symbols-outlined text-[15px] text-primary-300">{{ $pill['icon'] }}</span>
                        {{ $pill['label'] }}
                    </span>
                    @endforeach
                </div>

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('public.contacto') }}"
                       class="group inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-sm font-bold bg-brand text-white hover:bg-primary-700 active:bg-primary-800 transition-all duration-200 shadow-lg shadow-brand/30 hover:shadow-xl hover:shadow-brand/40 hover:scale-[1.02]">
                        <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                        Cotizar ahora
                        <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform duration-200">arrow_forward</span>
                    </a>
                    <a href="{{ route('public.servicios') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-sm font-semibold bg-white/[0.08] border border-white/20 text-white hover:bg-white/[0.14] transition-all duration-200">
                        <span class="material-symbols-outlined text-[18px]">play_circle</span>
                        Ver soluciones
                    </a>
                </div>
            </div>

            {{-- ── Dashboard mockup ─────────────────────────────────── --}}
            <div class="relative hidden lg:block" data-aos="fade-left" data-aos-duration="700" data-aos-delay="100">

                {{-- Glow --}}
                <div class="absolute -inset-4 bg-gradient-to-r from-brand/30 to-primary-700/20 rounded-3xl blur-2xl opacity-60"></div>

                {{-- Panel principal --}}
                <div class="relative bg-white/[0.04] backdrop-blur-sm border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
                    {{-- Topbar --}}
                    <div class="flex items-center justify-between px-4 py-3 bg-white/[0.06] border-b border-white/[0.08]">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-danger/80"></div>
                            <div class="w-3 h-3 rounded-full bg-warning/80"></div>
                            <div class="w-3 h-3 rounded-full bg-success/80"></div>
                        </div>
                        <span class="text-xs text-primary-300/70 font-medium">JVJ Technology — Dashboard</span>
                        <div class="w-16"></div>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- KPI cards --}}
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([
                                ['label'=>'Ventas hoy',   'value'=>'$12,450', 'delta'=>'+15%', 'color'=>'text-success', 'bg'=>'bg-success/10'],
                                ['label'=>'Pedidos',       'value'=>'156',     'delta'=>'+8',   'color'=>'text-info',    'bg'=>'bg-info/10'],
                            ] as $kpi)
                            <div class="bg-white/[0.05] border border-white/[0.08] rounded-xl p-4 hover:bg-white/[0.08] transition-colors duration-200">
                                <p class="text-[11px] text-primary-300/60 mb-1">{{ $kpi['label'] }}</p>
                                <p class="text-xl font-black text-white">{{ $kpi['value'] }}</p>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $kpi['color'] }} {{ $kpi['bg'] }} px-1.5 py-0.5 rounded-md mt-1">
                                    <span class="material-symbols-outlined text-[12px]">trending_up</span>
                                    {{ $kpi['delta'] }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Gráfico de barras simulado --}}
                        <div class="bg-white/[0.05] border border-white/[0.08] rounded-xl p-4">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[11px] text-primary-300/60 font-medium">Inventario semanal</span>
                                <span class="text-xs font-bold text-success bg-success/10 px-2 py-0.5 rounded-lg">+12%</span>
                            </div>
                            <div class="flex items-end gap-1.5 h-14">
                                @foreach([75,50,87,63,100,40,92] as $i => $h)
                                <div class="flex-1 rounded-t-sm bar-anim"
                                     style="height:{{ $h }}%;background:linear-gradient(to top,#1d4ed8,#3b82f6);animation-delay:{{ $i * 0.08 }}s">
                                </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between mt-2">
                                @foreach(['L','M','X','J','V','S','D'] as $d)
                                <span class="flex-1 text-center text-[9px] text-primary-400/50">{{ $d }}</span>
                                @endforeach
                            </div>
                        </div>

                        {{-- App conectada --}}
                        <div class="flex items-center gap-3 bg-brand/10 border border-brand/20 rounded-xl px-4 py-3 hover:bg-brand/15 transition-colors duration-200">
                            <div class="w-9 h-9 bg-brand rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-brand/30">
                                <span class="material-symbols-outlined text-white text-[18px]">smartphone</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-white">App conectada</p>
                                <p class="text-xs text-primary-300/60">Sincronización en tiempo real</p>
                            </div>
                            <span class="ml-auto w-2 h-2 rounded-full bg-success animate-pulse shrink-0"></span>
                        </div>
                    </div>
                </div>

                {{-- Floating chips --}}
                <div class="absolute -top-4 -right-6 flex items-center gap-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/10 px-3 py-2 rounded-xl shadow-xl animate-float">
                    <span class="material-symbols-outlined text-success text-[18px] ms-filled">check_circle</span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Sistema activo</span>
                </div>
                <div class="absolute -bottom-4 -left-6 flex items-center gap-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/10 px-3 py-2 rounded-xl shadow-xl animate-float delay-2000">
                    <span class="material-symbols-outlined text-brand text-[18px]">trending_up</span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">+40% productividad</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     SERVICIOS PRINCIPALES
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Nuestras soluciones
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">
                Tecnología que impulsa <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand to-primary-400">tu negocio</span>
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto text-lg">
                Software a medida que se adapta exactamente a cómo funciona tu empresa
            </p>
        </div>

        {{-- Cards --}}
        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                [
                    'icon'   => 'storage',
                    'title'  => 'Desarrollo ERP',
                    'desc'   => 'Sistemas de planificación empresarial personalizados para optimizar cada proceso de tu negocio.',
                    'items'  => ['Módulos 100% personalizables','Integración con sistemas existentes','Reportes en tiempo real'],
                    'route'  => 'public.servicios.desarrollo',
                    'accent' => 'from-brand to-primary-700',
                    'light'  => 'bg-primary-50 dark:bg-primary-950/40',
                    'icon_c' => 'text-brand',
                    'link_c' => 'text-brand dark:text-primary-400',
                    'delay'  => 0,
                ],
                [
                    'icon'   => 'smartphone',
                    'title'  => 'Apps Móviles',
                    'desc'   => 'Apps nativas e híbridas conectadas a tu ERP para gestionar tu negocio desde cualquier lugar.',
                    'items'  => ['iOS y Android','Sincronización en tiempo real','Modo sin conexión'],
                    'route'  => 'public.servicios.movil',
                    'accent' => 'from-emerald-600 to-teal-600',
                    'light'  => 'bg-emerald-50 dark:bg-emerald-950/40',
                    'icon_c' => 'text-emerald-600 dark:text-emerald-400',
                    'link_c' => 'text-emerald-600 dark:text-emerald-400',
                    'delay'  => 80,
                ],
                [
                    'icon'   => 'headset_mic',
                    'title'  => 'Soporte Técnico',
                    'desc'   => 'Acompañamiento especializado para garantizar el funcionamiento óptimo de tus sistemas.',
                    'items'  => ['Atención personalizada','Mantenimiento preventivo','SLA garantizado'],
                    'route'  => 'public.servicios.soporte',
                    'accent' => 'from-amber-500 to-orange-500',
                    'light'  => 'bg-amber-50 dark:bg-amber-950/40',
                    'icon_c' => 'text-amber-600 dark:text-amber-400',
                    'link_c' => 'text-amber-600 dark:text-amber-400',
                    'delay'  => 160,
                ],
            ] as $card)
            <div class="group card-shine relative bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.06] shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
                 data-aos="fade-up" data-aos-delay="{{ $card['delay'] }}">

                {{-- Gradient overlay on hover --}}
                <div class="absolute inset-0 bg-gradient-to-br {{ $card['accent'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <div class="relative z-10 p-8">
                    <div class="w-14 h-14 {{ $card['light'] }} group-hover:bg-white/20 rounded-2xl flex items-center justify-center mb-6 transition-colors duration-300">
                        <span class="material-symbols-outlined {{ $card['icon_c'] }} group-hover:text-white text-[28px] transition-colors duration-300"
                              style="font-variation-settings:'FILL' 0,'wght' 300">{{ $card['icon'] }}</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-white mb-3 transition-colors duration-300">
                        {{ $card['title'] }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 group-hover:text-white/80 text-sm leading-relaxed mb-5 transition-colors duration-300">
                        {{ $card['desc'] }}
                    </p>
                    <ul class="space-y-2 mb-6">
                        @foreach($card['items'] as $item)
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-white/80 transition-colors duration-300">
                            <span class="material-symbols-outlined text-success group-hover:text-white text-[16px] ms-filled shrink-0">check_circle</span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route($card['route']) }}"
                       class="inline-flex items-center gap-1.5 text-sm font-semibold {{ $card['link_c'] }} group-hover:text-white transition-colors duration-300 hover:gap-2.5">
                        Más información
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     MÓDULOS ERP
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Módulos ERP
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">
                Soluciones modulares para tu negocio
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Elige los módulos que necesitas o intégralos todos en un sistema completo
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['icon'=>'request_quote','label'=>'Cotizaciones',  'desc'=>'Genera y gestiona cotizaciones profesionales',         'route'=>'public.ejemplos.cotizaciones', 'accent'=>'bg-primary-600',     'light'=>'bg-primary-50 dark:bg-primary-950/50',  'icon_c'=>'text-primary-600 dark:text-primary-400',  'demo'=>true],
                ['icon'=>'receipt_long', 'label'=>'Facturación',   'desc'=>'Facturación electrónica y control de pagos',           'route'=>'public.ejemplos.facturacion',  'accent'=>'bg-emerald-600',     'light'=>'bg-emerald-50 dark:bg-emerald-950/50',  'icon_c'=>'text-success',                            'demo'=>true],
                ['icon'=>'inventory_2',  'label'=>'Inventario',    'desc'=>'Control de stock y alertas inteligentes',              'route'=>'public.ejemplos.inventario',   'accent'=>'bg-amber-500',       'light'=>'bg-amber-50 dark:bg-amber-950/50',      'icon_c'=>'text-warning',                            'demo'=>true],
                ['icon'=>'people',       'label'=>'CRM',           'desc'=>'Gestión de clientes y oportunidades de venta',         'route'=>'public.contacto',              'accent'=>'bg-purple-600',      'light'=>'bg-purple-50 dark:bg-purple-950/50',    'icon_c'=>'text-purple-600 dark:text-purple-400',    'demo'=>false],
            ] as $i => $mod)
            <div class="group relative bg-background-light dark:bg-background-dark rounded-2xl border border-gray-100 dark:border-white/[0.06] p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 overflow-hidden"
                 data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">

                {{-- Top accent bar --}}
                <div class="absolute top-0 inset-x-0 h-0.5 {{ $mod['accent'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <div class="w-12 h-12 {{ $mod['light'] }} rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined {{ $mod['icon_c'] }} text-[22px]"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $mod['icon'] }}</span>
                </div>

                <h3 class="font-black text-gray-900 dark:text-white mb-1.5 group-hover:text-brand dark:group-hover:text-primary-400 transition-colors duration-200">
                    {{ $mod['label'] }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-4">{{ $mod['desc'] }}</p>

                @if($mod['demo'])
                <a href="{{ route($mod['route']) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold text-brand dark:text-primary-400 hover:gap-2 transition-all duration-200">
                    Ver demo
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
                @else
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-white/[0.06] px-2.5 py-1 rounded-lg cursor-default">
                    <span class="material-symbols-outlined text-[13px]">schedule</span>
                    Próximamente
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     METODOLOGÍA (nuevo — diferenciador)
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div data-aos="fade-right">
                <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-5">
                    Nuestra diferencia
                </span>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                    Escuchamos primero,<br>programamos después
                </h2>
                <p class="text-gray-500 dark:text-gray-400 leading-relaxed mb-8">
                    Antes de escribir una sola línea de código, nos sentamos con cada trabajador para entender su día a día. El resultado es un sistema que el equipo realmente usa porque fue diseñado para ellos.
                </p>

                <div class="space-y-4">
                    @foreach([
                        ['num'=>'01','icon'=>'request_quote', 'title'=>'Cotización precisa',   'desc'=>'Presupuesto claro y sin letra chica desde el inicio.'],
                        ['num'=>'02','icon'=>'groups',        'title'=>'Inmersión total',       'desc'=>'Hablamos con cada persona del equipo para entender el flujo real.'],
                        ['num'=>'03','icon'=>'code',          'title'=>'Desarrollo modular',   'desc'=>'Construimos pieza por pieza, mostrando avances constantes.'],
                        ['num'=>'04','icon'=>'school',        'title'=>'Capacitación incluida','desc'=>'No entregamos código y nos vamos — te enseñamos hasta que domines el sistema.'],
                    ] as $step)
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-white/[0.06] hover:border-primary-200 dark:hover:border-primary-800 transition-colors duration-200"
                         data-aos="fade-right" data-aos-delay="{{ $loop->index * 60 }}">
                        <div class="w-10 h-10 rounded-xl bg-brand flex items-center justify-center shrink-0 shadow-sm shadow-brand/20">
                            <span class="material-symbols-outlined text-white text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 300">{{ $step['icon'] }}</span>
                        </div>
                        <div>
                            <p class="font-black text-gray-900 dark:text-white text-sm">
                                <span class="text-primary-400 dark:text-primary-500 mr-1.5 text-[11px] font-bold">{{ $step['num'] }}</span>
                                {{ $step['title'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div data-aos="fade-left" data-aos-delay="100">
                {{-- Stats panel --}}
                <div class="bg-primary-950 dark:bg-gray-900 rounded-3xl p-8 shadow-2xl border border-white/[0.06]">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        @foreach([
                            ['val'=>'100%','label'=>'Software a medida',      'icon'=>'settings'],
                            ['val'=>'24/7', 'label'=>'Soporte disponible',    'icon'=>'support_agent'],
                            ['val'=>'+3',   'label'=>'Años de experiencia',   'icon'=>'workspace_premium'],
                            ['val'=>'∞',    'label'=>'Escalabilidad',          'icon'=>'trending_up'],
                        ] as $stat)
                        <div class="bg-white/[0.05] hover:bg-white/[0.09] border border-white/[0.08] rounded-2xl p-5 transition-colors duration-200">
                            <span class="material-symbols-outlined text-primary-400 text-[22px] mb-2 block"
                                  style="font-variation-settings:'FILL' 0,'wght' 200">{{ $stat['icon'] }}</span>
                            <p class="text-2xl font-black text-white">{{ $stat['val'] }}</p>
                            <p class="text-xs text-primary-300/60 mt-0.5">{{ $stat['label'] }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="bg-brand/20 border border-brand/30 rounded-xl px-4 py-3 flex items-center gap-3">
                        <span class="material-symbols-outlined text-brand text-[20px] shrink-0">verified</span>
                        <p class="text-sm text-primary-200/80">
                            <span class="font-bold text-white">JCB, Gavsa, JGV</span> y otras empresas líderes confían en nuestras soluciones.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     TESTIMONIOS
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Testimonios
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">
                Lo que dicen nuestros clientes
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                Empresas que transformaron su operación con nuestras soluciones
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-14">
            @foreach([
                ['name'=>'Carlos López',   'role'=>'Gerente, Distribuidora López',  'quote'=>'El ERP transformó completamente nuestra operación. Redujimos tiempos de proceso en un 60% y ahora tenemos control total del inventario desde nuestros móviles.',  'stat'=>'+60% eficiencia',   'color'=>'text-brand'],
                ['name'=>'María González', 'role'=>'Directora Comercial, Grupo MG', 'quote'=>'La app móvil conectada al ERP permitió a nuestros vendedores tomar pedidos en ruta. Incrementamos las ventas un 40% en el primer trimestre.',                     'stat'=>'+40% ventas',       'color'=>'text-success'],
                ['name'=>'Juan Rodríguez', 'role'=>'Dueño, Ferretería Rodríguez',   'quote'=>'El módulo de inventario en tiempo real nos ahorró miles en pérdidas. Las alertas automáticas evitan desabastos y sobreinventario por completo.',                 'stat'=>'-30% pérdidas',     'color'=>'text-warning'],
            ] as $i => $t)
            <div class="bg-background-light dark:bg-background-dark rounded-2xl p-6 border border-gray-100 dark:border-white/[0.06] hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                {{-- Stars --}}
                <div class="flex gap-1 mb-4">
                    @for($s = 0; $s < 5; $s++)
                    <span class="material-symbols-outlined text-warning text-[18px] ms-filled">star</span>
                    @endfor
                </div>

                <blockquote class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed italic mb-5">
                    "{{ $t['quote'] }}"
                </blockquote>

                <div class="flex items-center justify-between border-t border-gray-100 dark:border-white/[0.06] pt-4">
                    <div class="flex items-center gap-3">
                        {{-- Avatar initials --}}
                        <div class="w-9 h-9 rounded-full bg-brand flex items-center justify-center text-white text-xs font-black shrink-0">
                            {{ mb_substr($t['name'], 0, 1) }}{{ mb_substr(explode(' ', $t['name'])[1] ?? '', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $t['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $t['role'] }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold {{ $t['color'] }} bg-current/10 px-2.5 py-1 rounded-lg whitespace-nowrap">
                        {{ $t['stat'] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Logos de clientes --}}
        <div class="text-center" data-aos="fade-up">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-6">
                Empresas que confían en nosotros
            </p>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12">
                @foreach([
                    ['src'=>'logos/logo-jgv.jpg',   'alt'=>'JGV'],
                    ['src'=>'logos/logo-jcb.webp',  'alt'=>'JCB'],
                    ['src'=>'logos/logo-gavsa.jpeg', 'alt'=>'Gavsa'],
                ] as $logo)
                <img src="{{ asset($logo['src']) }}" alt="{{ $logo['alt'] }}"
                     class="h-10 md:h-12 w-auto max-w-[120px] object-contain opacity-50 dark:opacity-30 hover:opacity-90 dark:hover:opacity-70 hover:scale-110 transition-all duration-300 rounded-lg grayscale hover:grayscale-0">
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     BLOG / RECURSOS
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Recursos
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">
                Artículos y novedades
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                Consejos y tendencias sobre ERP, transformación digital y tecnología empresarial
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['img'=>'https://placehold.co/600x280/1e40af/ffffff?text=ERP',           'tag'=>'Artículo',  'tag_c'=>'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/60', 'title'=>'5 beneficios de implementar un ERP en tu empresa',     'desc'=>'Descubre cómo un ERP puede optimizar tus procesos y aumentar la productividad de tu equipo.',    'delay'=>0],
                ['img'=>'https://placehold.co/600x280/059669/ffffff?text=Mobile',         'tag'=>'Guía',      'tag_c'=>'text-success bg-emerald-50 dark:bg-emerald-950/60',                           'title'=>'Cómo elegir la mejor app móvil para tu negocio',       'desc'=>'Factores clave a considerar cuando desarrollas una aplicación empresarial conectada.',           'delay'=>80],
                ['img'=>'https://placehold.co/600x280/6d28d9/ffffff?text=Transformacion', 'tag'=>'Tutorial',  'tag_c'=>'text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60',     'title'=>'Pasos para una transformación digital exitosa',         'desc'=>'Guía práctica para digitalizar los procesos de tu empresa sin perder de vista a las personas.',  'delay'=>160],
            ] as $post)
            <article class="group bg-surface-light dark:bg-surface-dark rounded-2xl overflow-hidden border border-gray-100 dark:border-white/[0.06] hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300"
                     data-aos="fade-up" data-aos-delay="{{ $post['delay'] }}">
                <div class="overflow-hidden h-44">
                    <img src="{{ $post['img'] }}" alt="{{ $post['title'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <span class="inline-block text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg {{ $post['tag_c'] }} mb-3">
                        {{ $post['tag'] }}
                    </span>
                    <h3 class="font-black text-gray-900 dark:text-white mb-2 leading-tight group-hover:text-brand dark:group-hover:text-primary-400 transition-colors duration-200">
                        {{ $post['title'] }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-4">
                        {{ $post['desc'] }}
                    </p>
                    <a href="#" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand dark:text-primary-400 hover:gap-2.5 transition-all duration-200">
                        Leer más
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     CTA FINAL
══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-24">
    {{-- Blobs --}}
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-15 animate-blob"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-10 animate-blob delay-2000"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-aos="fade-up">
        <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-primary-200 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6">
            <span class="material-symbols-outlined text-[14px] text-warning ms-filled">star</span>
            Consultoría gratuita
        </span>

        <h2 class="text-3xl md:text-4xl lg:text-5xl font-black mb-5 leading-tight">
            ¿Listo para digitalizar<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">tus procesos?</span>
        </h2>

        <p class="text-lg text-primary-200/80 mb-10 max-w-2xl mx-auto leading-relaxed">
            Agenda una consultoría sin costo y descubre cómo nuestras soluciones se adaptan exactamente a tu empresa.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('public.contacto') }}"
               class="group inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-sm font-bold bg-brand text-white hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 shadow-xl shadow-brand/30 hover:shadow-brand/50 hover:scale-[1.02]">
                <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                Agendar consultoría gratuita
                <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
            <a href="tel:+529931234567"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-sm font-semibold bg-white/[0.08] border border-white/20 text-white hover:bg-white/[0.14] transition-all duration-200">
                <span class="material-symbols-outlined text-[20px]">call</span>
                Llamar ahora
            </a>
        </div>
    </div>
</section>

@endsection
