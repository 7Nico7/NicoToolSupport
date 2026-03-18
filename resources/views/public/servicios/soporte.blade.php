{{-- file: resources/views/public/servicios/soporte.blade.php --}}
@extends('layouts.public')
@section('title', 'Soporte y Mantenimiento — JVJ Technology')
@section('meta_description', 'Planes de soporte técnico y mantenimiento para sistemas web y apps móviles. Desde horario laboral hasta atención 24/7 con SLA garantizado.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-amber-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">headset_mic</span>
                Servicio
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Soporte y<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-orange-300">Mantenimiento</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Acompañamiento continuo para tus apps web y móviles. Garantizamos su funcionamiento óptimo y evolución constante.
            </p>
        </div>
    </div>
</section>

{{-- POR QUÉ ES IMPORTANTE --}}
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <span class="inline-block text-warning text-xs font-black uppercase tracking-widest mb-3">Por qué importa</span>
                <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                    Tu software<br>siempre saludable
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-[15px] leading-relaxed mb-8">
                    Un software no termina cuando se entrega. Ofrecemos planes de soporte y mantenimiento para asegurar que tus sistemas estén siempre actualizados, seguros y funcionando,
                    <strong class="text-gray-900 dark:text-white font-semibold">permitiéndote enfocarte en tu negocio.</strong>
                </p>
                <div class="space-y-4">
                    @foreach([
                        ['icon'=>'security',       'title'=>'Protección continua',    'desc'=>'Seguridad y actualizaciones para ambas plataformas'],
                        ['icon'=>'autorenew',       'title'=>'Evolución constante',    'desc'=>'Mejoras, nuevas funcionalidades y adaptaciones'],
                        ['icon'=>'support_agent',   'title'=>'Atención especializada', 'desc'=>'Soporte técnico personalizado para web y móvil'],
                        ['icon'=>'monitoring',      'title'=>'Monitoreo proactivo',    'desc'=>'Detectamos problemas antes de que afecten la operación'],
                    ] as $feat)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 bg-amber-50 dark:bg-amber-950/40 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-warning text-[17px]"
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
                    <img src="https://placehold.co/600x420/78350f/ffffff?text=Soporte+Web+y+Móvil"
                         alt="Soporte JVJ" class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-amber-950/40 to-transparent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- SOPORTE WEB --}}
<section class="py-20 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3 mb-10" data-aos="fade-up">
            <div class="w-11 h-11 bg-primary-50 dark:bg-primary-950/60 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-brand dark:text-primary-400 text-[22px]"
                      style="font-variation-settings:'FILL' 0,'wght' 300">storage</span>
            </div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white">Soporte para Aplicaciones Web</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['icon'=>'dns',           'title'=>'Mantenimiento de servidores',    'desc'=>'Optimización, monitoreo y actualizaciones del entorno de hosting'],
                ['icon'=>'lock',          'title'=>'Parches de seguridad',           'desc'=>'Actualizaciones de frameworks, librerías y dependencias críticas'],
                ['icon'=>'database',      'title'=>'Respaldos de base de datos',     'desc'=>'Backups automáticos y recuperación ante desastres garantizada'],
                ['icon'=>'speed',         'title'=>'Optimización de rendimiento',    'desc'=>'Mejora de velocidad de carga y tiempos de respuesta del sistema'],
                ['icon'=>'bug_report',    'title'=>'Corrección de errores',          'desc'=>'Detección y solución ágil de incidentes en producción'],
                ['icon'=>'code',          'title'=>'Actualizaciones de frameworks',  'desc'=>'Compatibilidad con nuevas versiones de Laravel, Django, Phalcon, React'],
            ] as $i => $item)
            <div class="flex items-start gap-3 p-5 bg-background-light dark:bg-background-dark rounded-2xl
                        border border-gray-100 dark:border-white/[0.07]
                        hover:border-brand dark:hover:border-primary-600 transition-all duration-200"
                 data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                <div class="w-9 h-9 bg-primary-50 dark:bg-primary-950/60 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-brand dark:text-primary-400 text-[17px]"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $item['icon'] }}</span>
                </div>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $item['title'] }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5 leading-relaxed">{{ $item['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SOPORTE MÓVIL --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3 mb-10" data-aos="fade-up">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[22px]"
                      style="font-variation-settings:'FILL' 0,'wght' 300">smartphone</span>
            </div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white">Soporte para Aplicaciones Móviles</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['icon'=>'fab fa-apple',  'ms'=>false, 'title'=>'Actualizaciones iOS',         'desc'=>'Compatibilidad con nuevas versiones de iOS y dispositivos Apple'],
                ['icon'=>'fab fa-android','ms'=>false, 'title'=>'Actualizaciones Android',     'desc'=>'Adaptación a nuevas versiones de Android y fragmentación de dispositivos'],
                ['icon'=>'shop',          'ms'=>true,  'title'=>'Publicación en stores',       'desc'=>'Gestión de publicaciones en App Store y Google Play'],
                ['icon'=>'devices',       'ms'=>true,  'title'=>'Compatibilidad de dispositivos','desc'=>'Adaptación a nuevas cámaras, sensores y versiones de hardware'],
                ['icon'=>'bug_report',    'ms'=>true,  'title'=>'Corrección de errores',       'desc'=>'Solución de crashes y problemas específicos de plataforma'],
                ['icon'=>'code',          'ms'=>true,  'title'=>'Actualizaciones de frameworks','desc'=>'Compatibilidad con nuevas versiones de React Native y Flutter'],
            ] as $i => $item)
            <div class="flex items-start gap-3 p-5 bg-surface-light dark:bg-surface-dark rounded-2xl
                        border border-gray-100 dark:border-white/[0.07]
                        hover:border-emerald-500 dark:hover:border-emerald-600 transition-all duration-200"
                 data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    @if($item['ms'])
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[17px]"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $item['icon'] }}</span>
                    @else
                    <i class="{{ $item['icon'] }} text-emerald-600 dark:text-emerald-400 text-[14px]"></i>
                    @endif
                </div>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $item['title'] }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5 leading-relaxed">{{ $item['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SERVICIOS TRANSVERSALES --}}
<section class="py-16 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="inline-block bg-amber-50 dark:bg-amber-950/40 text-warning text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Ambas plataformas
            </span>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-3">Servicios transversales</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Independientemente de la plataforma, estos servicios aplican tanto a web como a móvil
            </p>
        </div>
        <div class="grid sm:grid-cols-3 gap-5 max-w-3xl mx-auto">
            @foreach([
                ['icon'=>'headset_mic',    'title'=>'Mesa de ayuda',  'desc'=>'Atención a usuarios y resolución de dudas y reportes'],
                ['icon'=>'trending_up',    'title'=>'Asesoría',       'desc'=>'Te ayudamos a sacar más provecho de tu sistema'],
                ['icon'=>'school',         'title'=>'Capacitación',   'desc'=>'Entrenamiento para nuevos usuarios o funciones adicionales'],
            ] as $i => $s)
            <div class="bg-background-light dark:bg-background-dark rounded-2xl p-6 text-center
                        border border-gray-100 dark:border-white/[0.07]
                        hover:border-warning dark:hover:border-amber-600 hover:shadow-md transition-all duration-200"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/40 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-warning text-[22px]"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $s['icon'] }}</span>
                </div>
                <h3 class="font-black text-gray-900 dark:text-white mb-2">{{ $s['title'] }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PLANES --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-amber-50 dark:bg-amber-950/40 text-warning text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Planes
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">Planes personalizados</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Adaptamos el servicio a tus necesidades y presupuesto. Desde soporte básico hasta atención 24/7 con SLA garantizado.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            @foreach([
                [
                    'icon'=>'work_history', 'title'=>'Horario laboral',
                    'sub'=>'Ideal para negocios con operación de oficina',
                    'items'=>['Atención en horario de oficina','Respuesta en 24–48 hrs','Mantenimiento preventivo'],
                    'featured'=>false,
                ],
                [
                    'icon'=>'star',         'title'=>'Prioritario',
                    'sub'=>'Respuesta rápida y atención personalizada',
                    'items'=>['Atención 12×5','Respuesta en 4–8 hrs','Actualizaciones incluidas','Gerente de cuenta asignado'],
                    'featured'=>true,
                ],
                [
                    'icon'=>'schedule',     'title'=>'24/7',
                    'sub'=>'Para operaciones críticas que no pueden parar',
                    'items'=>['Atención 24/7/365','Respuesta en 1–2 hrs','SLA garantizado','Monitoreo continuo'],
                    'featured'=>false,
                ],
            ] as $plan)
            <div class="relative rounded-2xl p-8 text-center transition-all duration-200
                        {{ $plan['featured']
                            ? 'bg-warning border-2 border-warning shadow-xl shadow-warning/20'
                            : 'bg-surface-light dark:bg-surface-dark border-2 border-gray-100 dark:border-white/[0.07] hover:border-warning dark:hover:border-amber-500' }}"
                 data-aos="fade-up">
                @if($plan['featured'])
                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-primary-950 text-warning text-[10px] font-black px-4 py-1 rounded-full uppercase tracking-widest border border-warning/30">
                    Recomendado
                </span>
                @endif

                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center
                            {{ $plan['featured'] ? 'bg-white/20' : 'bg-amber-50 dark:bg-amber-950/40' }}">
                    <span class="material-symbols-outlined text-[26px] {{ $plan['featured'] ? 'text-white' : 'text-warning' }}"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $plan['icon'] }}</span>
                </div>
                <h3 class="text-xl font-black mb-2 {{ $plan['featured'] ? 'text-white' : 'text-gray-900 dark:text-white' }}">
                    {{ $plan['title'] }}
                </h3>
                <p class="text-sm mb-6 {{ $plan['featured'] ? 'text-amber-100' : 'text-gray-500 dark:text-gray-400' }}">
                    {{ $plan['sub'] }}
                </p>
                <ul class="space-y-2 mb-8 text-left">
                    @foreach($plan['items'] as $item)
                    <li class="flex items-center gap-2 text-sm {{ $plan['featured'] ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}">
                        <span class="material-symbols-outlined text-[16px] ms-filled {{ $plan['featured'] ? 'text-white' : 'text-success' }}">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('public.contacto') }}"
                   class="block w-full py-2.5 rounded-xl text-sm font-bold transition-all duration-200
                          {{ $plan['featured']
                              ? 'bg-white text-warning hover:bg-amber-50'
                              : 'bg-warning text-white hover:bg-amber-600' }}">
                    Solicitar cotización
                </a>
            </div>
            @endforeach
        </div>

        <p class="text-center text-gray-400 dark:text-gray-500 mt-8 text-sm" data-aos="fade-up">
            Todos los planes se cotizan según el volumen y complejidad de tu operación.
            <a href="{{ route('public.contacto') }}" class="text-warning font-bold hover:underline ml-1">
                Contáctanos para una propuesta personalizada.
            </a>
        </p>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-15"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-amber-700 rounded-full blur-3xl opacity-10"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-black mb-4">¿Necesitas soporte para tu sistema?</h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Cuéntanos qué plataforma usas y te prepararemos una propuesta a tu medida.
        </p>
        <a href="{{ route('public.contacto') }}"
           class="group inline-flex items-center gap-2 px-8 py-4 bg-brand text-white font-black rounded-xl
                  hover:bg-primary-600 active:bg-primary-700 transition-all duration-200
                  shadow-xl shadow-brand/30 hover:shadow-brand/50 hover:scale-[1.02]">
            <span class="material-symbols-outlined text-[20px]">headset_mic</span>
            Solicitar cotización
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>
    </div>
</section>

@endsection
