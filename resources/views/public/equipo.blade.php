@extends('layouts.public')
@section('title', 'Nuestro Equipo — JVJ Technology')
@section('meta_description', 'Conoce al equipo de JVJ Technology: desarrolladores, diseñadores y expertos en tecnología que hacen posible cada proyecto.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">groups</span>
                Las personas detrás
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Nuestro<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">Equipo</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Conoce a los profesionales que hacen posible cada proyecto que construimos.
            </p>
        </div>
    </div>
</section>

{{-- EQUIPO GRID --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['initials'=>'JV','name'=>'Juan Vásquez',   'role'=>'CEO & Fundador',       'bio'=>'Líder visionario con más de 10 años en el sector tecnológico.',           'bg'=>'bg-brand',        'social'=>[['fab fa-linkedin-in','#'],['fab fa-twitter','#'],['fas fa-envelope','#']]],
                ['initials'=>'JJ','name'=>'José Jiménez',   'role'=>'CTO & Co-fundador',    'bio'=>'Arquitecto de software especializado en sistemas escalables.',             'bg'=>'bg-primary-800',  'social'=>[['fab fa-linkedin-in','#'],['fab fa-github','#'],['fas fa-envelope','#']]],
                ['initials'=>'JT','name'=>'Javier Torres',  'role'=>'Director de Proyectos','bio'=>'Project Manager certificado con experiencia internacional.',               'bg'=>'bg-primary-600',  'social'=>[['fab fa-linkedin-in','#'],['fas fa-envelope','#']]],
                ['initials'=>'AR','name'=>'Ana Rodríguez',  'role'=>'Lead Developer',       'bio'=>'Experta en desarrollo backend y bases de datos relacionales.',             'bg'=>'bg-indigo-600',   'social'=>[['fab fa-linkedin-in','#'],['fab fa-github','#'],['fas fa-envelope','#']]],
                ['initials'=>'CM','name'=>'Carlos Mendoza', 'role'=>'Desarrollador Móvil',  'bio'=>'Especialista en iOS y Android con 8 años de experiencia.',                'bg'=>'bg-violet-600',   'social'=>[['fab fa-linkedin-in','#'],['fab fa-apple','#'],['fab fa-android','#']]],
                ['initials'=>'LG','name'=>'Laura Gómez',    'role'=>'UX/UI Designer',       'bio'=>'Crea experiencias de usuario intuitivas y visualmente atractivas.',        'bg'=>'bg-purple-600',   'social'=>[['fab fa-linkedin-in','#'],['fab fa-behance','#'],['fas fa-envelope','#']]],
                ['initials'=>'PR','name'=>'Pedro Ramírez',  'role'=>'DevOps Engineer',      'bio'=>'Garantiza la estabilidad y escalabilidad de nuestras aplicaciones.',       'bg'=>'bg-fuchsia-600',  'social'=>[['fab fa-linkedin-in','#'],['fab fa-docker','#'],['fas fa-envelope','#']]],
                ['initials'=>'SF','name'=>'Sofía Fuentes',  'role'=>'QA Engineer',          'bio'=>'Asegura la calidad y el rendimiento óptimo de cada desarrollo.',           'bg'=>'bg-pink-600',     'social'=>[['fab fa-linkedin-in','#'],['fas fa-bug','#'],['fas fa-envelope','#']]],
            ] as $i => $m)
            <div class="group bg-surface-light dark:bg-surface-dark rounded-2xl overflow-hidden
                        border border-gray-100 dark:border-white/[0.07]
                        hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">

                {{-- Avatar --}}
                <div class="relative h-48 {{ $m['bg'] }} flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <span class="text-5xl font-black text-white/90 relative z-10 tracking-tighter">{{ $m['initials'] }}</span>
                    {{-- Decorative circles --}}
                    <div class="absolute -bottom-6 -right-6 w-20 h-20 rounded-full bg-white/10"></div>
                    <div class="absolute -top-4 -left-4 w-16 h-16 rounded-full bg-white/10"></div>
                </div>

                <div class="p-5">
                    <h3 class="font-black text-gray-900 dark:text-white text-base leading-tight">{{ $m['name'] }}</h3>
                    <p class="text-brand dark:text-primary-400 text-xs font-bold mb-2">{{ $m['role'] }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs leading-relaxed mb-4">{{ $m['bio'] }}</p>
                    <div class="flex gap-2">
                        @foreach($m['social'] as [$icon,$href])
                        <a href="{{ $href }}"
                           class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-white/[0.06] hover:bg-brand hover:text-white
                                  flex items-center justify-center text-gray-400 dark:text-gray-500
                                  text-[11px] transition-all duration-200">
                            <i class="{{ $icon }}"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA UNIRSE --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-15"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-10"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10" data-aos="fade-up">
        <span class="material-symbols-outlined text-warning text-[40px] mb-4 block ms-filled">group_add</span>
        <h2 class="text-3xl md:text-4xl font-black mb-4">
            ¿Quieres formar parte del equipo?
        </h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Siempre estamos buscando talento apasionado por la tecnología y por resolver problemas reales.
        </p>
        <a href="mailto:talentos@jvjtechnology.com"
           class="group inline-flex items-center gap-2 px-8 py-4 bg-brand text-white font-black rounded-xl
                  hover:bg-primary-600 active:bg-primary-700 transition-all duration-200
                  shadow-xl shadow-brand/30 hover:shadow-brand/50 hover:scale-[1.02]">
            <span class="material-symbols-outlined text-[20px]">send</span>
            Envía tu CV
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>
    </div>
</section>

@endsection
