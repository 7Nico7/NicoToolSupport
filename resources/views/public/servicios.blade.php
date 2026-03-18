@extends('layouts.public')
@section('title', 'Servicios — JVJ Technology')
@section('meta_description', 'Desarrollo ERP a medida, apps móviles, consultoría tecnológica y soporte técnico para empresas. Conoce todos nuestros servicios.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">build</span>
                Qué hacemos
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Nuestros<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">Servicios</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Soluciones tecnológicas integrales diseñadas para impulsar la operación de tu empresa.
            </p>
        </div>
    </div>
</section>

{{-- SERVICIOS GRID --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-6">
            @foreach([
                [
                    'icon'=>'storage',      'title'=>'Desarrollo ERP a Medida',
                    'desc'=>'Sistemas de planificación de recursos empresariales personalizados para optimizar todos los procesos de tu negocio.',
                    'items'=>['Módulos 100% personalizables','Integración con sistemas existentes','Reportes y dashboards en tiempo real','Acceso desde cualquier dispositivo'],
                    'route'=>'public.servicios.desarrollo',
                    'accent'=>'from-brand to-primary-700', 'light'=>'bg-primary-50 dark:bg-primary-950/50', 'icon_c'=>'text-brand dark:text-primary-400', 'link_c'=>'text-brand dark:text-primary-400',
                    'delay'=>0,
                ],
                [
                    'icon'=>'smartphone',   'title'=>'Apps Móviles Conectadas',
                    'desc'=>'Aplicaciones para iOS y Android que se sincronizan en tiempo real con tu sistema ERP para gestionar desde cualquier lugar.',
                    'items'=>['iOS y Android nativo','Sincronización en tiempo real','Modo sin conexión (offline)','Notificaciones push inteligentes'],
                    'route'=>'public.servicios.movil',
                    'accent'=>'from-emerald-600 to-teal-600', 'light'=>'bg-emerald-50 dark:bg-emerald-950/50', 'icon_c'=>'text-emerald-600 dark:text-emerald-400', 'link_c'=>'text-emerald-600 dark:text-emerald-400',
                    'delay'=>60,
                ],
                [
                    'icon'=>'analytics',    'title'=>'Consultoría Tecnológica',
                    'desc'=>'Asesoramiento experto para optimizar tus procesos y elegir las mejores soluciones tecnológicas para tu industria.',
                    'items'=>['Análisis de procesos actuales','Mapa de digitalización','Selección de stack tecnológico','Acompañamiento en implementación'],
                    'route'=>'public.servicios.consultoria',
                    'accent'=>'from-violet-600 to-purple-700', 'light'=>'bg-violet-50 dark:bg-violet-950/50', 'icon_c'=>'text-violet-600 dark:text-violet-400', 'link_c'=>'text-violet-600 dark:text-violet-400',
                    'delay'=>120,
                ],
                [
                    'icon'=>'headset_mic',  'title'=>'Soporte y Mantenimiento',
                    'desc'=>'Acompañamiento continuo, soporte técnico y actualizaciones para garantizar el óptimo funcionamiento de tus sistemas.',
                    'items'=>['SLA garantizado','Mantenimiento preventivo','Actualizaciones de seguridad','Soporte de emergencia 24/7'],
                    'route'=>'public.servicios.soporte',
                    'accent'=>'from-amber-500 to-orange-500', 'light'=>'bg-amber-50 dark:bg-amber-950/50', 'icon_c'=>'text-amber-600 dark:text-amber-400', 'link_c'=>'text-amber-600 dark:text-amber-400',
                    'delay'=>180,
                ],
            ] as $card)
            <div class="group relative bg-surface-light dark:bg-surface-dark rounded-2xl p-8 overflow-hidden
                        border border-gray-100 dark:border-white/[0.07]
                        hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $card['delay'] }}">

                <div class="absolute inset-0 bg-gradient-to-br {{ $card['accent'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <div class="relative z-10 flex items-start gap-5">
                    <div class="w-14 h-14 {{ $card['light'] }} group-hover:bg-white/20 rounded-2xl flex items-center justify-center shrink-0 transition-colors duration-300">
                        <span class="material-symbols-outlined {{ $card['icon_c'] }} group-hover:text-white text-[28px] transition-colors duration-300"
                              style="font-variation-settings:'FILL' 0,'wght' 300">{{ $card['icon'] }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-white mb-2 transition-colors duration-300">
                            {{ $card['title'] }}
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 group-hover:text-white/80 text-sm leading-relaxed mb-4 transition-colors duration-300">
                            {{ $card['desc'] }}
                        </p>
                        <ul class="grid grid-cols-2 gap-x-4 gap-y-1 mb-5">
                            @foreach($card['items'] as $item)
                            <li class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 group-hover:text-white/80 transition-colors duration-300">
                                <span class="material-symbols-outlined text-success group-hover:text-white text-[14px] ms-filled shrink-0">check_circle</span>
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
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- METODOLOGÍA --}}
<section class="py-20 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Cómo trabajamos
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">Nuestra metodología</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Un proceso claro y transparente, de principio a fin
            </p>
        </div>

        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['num'=>'01','icon'=>'search',          'title'=>'Descubrimiento', 'desc'=>'Analizamos tus necesidades y procesos actuales con cada persona del equipo.'],
                ['num'=>'02','icon'=>'design_services', 'title'=>'Diseño',         'desc'=>'Creamos la arquitectura y el flujo de la solución basado en lo que encontramos.'],
                ['num'=>'03','icon'=>'code',            'title'=>'Desarrollo',     'desc'=>'Construimos módulo por módulo con metodologías ágiles y avances visibles.'],
                ['num'=>'04','icon'=>'rocket_launch',   'title'=>'Entrega',        'desc'=>'Implementamos, capacitamos y damos acompañamiento continuo post-lanzamiento.'],
            ] as $i => $step)
            <div class="bg-background-light dark:bg-background-dark rounded-2xl p-6 text-center
                        border border-gray-100 dark:border-white/[0.07]
                        hover:border-primary-200 dark:hover:border-primary-800 hover:shadow-md transition-all duration-200"
                 data-aos="fade-up" data-aos-delay="{{ $i * 70 }}">
                <div class="w-12 h-12 bg-brand rounded-xl flex items-center justify-center mx-auto mb-4 shadow-sm shadow-brand/20">
                    <span class="material-symbols-outlined text-white text-[20px]"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $step['icon'] }}</span>
                </div>
                <div class="text-[10px] font-black text-gray-300 dark:text-gray-600 mb-2">{{ $step['num'] }}</div>
                <h3 class="font-black text-gray-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-15"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-10"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-black mb-4">¿Cuál servicio necesitas?</h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Cuéntanos tu caso y te preparamos una propuesta a medida sin compromiso.
        </p>
        <a href="{{ route('public.contacto') }}"
           class="group inline-flex items-center gap-2 px-8 py-4 bg-brand text-white font-black rounded-xl
                  hover:bg-primary-600 active:bg-primary-700 transition-all duration-200
                  shadow-xl shadow-brand/30 hover:shadow-brand/50 hover:scale-[1.02]">
            <span class="material-symbols-outlined text-[20px]">send</span>
            Solicitar propuesta
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>
    </div>
</section>

@endsection
