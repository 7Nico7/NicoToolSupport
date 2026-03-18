@extends('layouts.public')
@section('title', 'Nuestros Valores — JVJ Technology')
@section('meta_description', 'Los valores que guían a JVJ Technology: compromiso, innovación, confianza, excelencia, trabajo en equipo y responsabilidad social.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">favorite</span>
                Lo que nos define
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Nuestros<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">Valores</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Los principios que guían cada decisión que tomamos, cada día.
            </p>
        </div>
    </div>
</section>

{{-- VALORES GRID --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                [
                    'icon'=>'favorite',  'title'=>'Compromiso',
                    'desc'=>'Nos entregamos por completo a cada proyecto. Desde la primera cotización hasta la última línea de código, los objetivos de nuestros clientes son los nuestros.',
                    'items'=>['Cumplimiento de plazos y presupuestos','Dedicación total a entender el negocio','Responsabilidad en cada etapa'],
                    'accent'=>'from-brand to-primary-700', 'light'=>'bg-primary-50 dark:bg-primary-950/50', 'icon_c'=>'text-brand dark:text-primary-400',
                ],
                [
                    'icon'=>'lightbulb', 'title'=>'Innovación',
                    'desc'=>'Buscamos constantemente nuevas formas de resolver problemas. No solo aplicamos tecnología, sino que repensamos los procesos junto a los trabajadores.',
                    'items'=>['Tecnologías de vanguardia','Mejora continua de procesos','Adaptación a las necesidades reales'],
                    'accent'=>'from-violet-600 to-purple-700', 'light'=>'bg-violet-50 dark:bg-violet-950/50', 'icon_c'=>'text-violet-600 dark:text-violet-400',
                ],
                [
                    'icon'=>'handshake', 'title'=>'Confianza',
                    'desc'=>'Construimos relaciones duraderas basadas en la transparencia. Mostramos avances constantemente para que no haya sorpresas.',
                    'items'=>['Comunicación transparente y avances visibles','Honestidad en los alcances del proyecto','Cumplimiento de acuerdos'],
                    'accent'=>'from-emerald-600 to-teal-600', 'light'=>'bg-emerald-50 dark:bg-emerald-950/50', 'icon_c'=>'text-emerald-600 dark:text-emerald-400',
                ],
                [
                    'icon'=>'star',      'title'=>'Excelencia',
                    'desc'=>'No nos conformamos con lo mínimo. Analizamos cada detalle del proceso para asegurarnos de que el sistema mejore la vida de quienes lo usan.',
                    'items'=>['Código de calidad y escalable','Pruebas exhaustivas con usuarios reales','Atención al detalle en la experiencia de uso'],
                    'accent'=>'from-amber-500 to-orange-500', 'light'=>'bg-amber-50 dark:bg-amber-950/50', 'icon_c'=>'text-amber-600 dark:text-amber-400',
                ],
                [
                    'icon'=>'groups',    'title'=>'Trabajo en equipo',
                    'desc'=>'Los mejores resultados se logran cuando colaboramos. Esto incluye a nuestro equipo y a los trabajadores de nuestros clientes, cuyo conocimiento es la base del proyecto.',
                    'items'=>['Colaboración estrecha con el cliente','Comunicación efectiva y respetuosa','Apoyo mutuo para superar desafíos'],
                    'accent'=>'from-sky-600 to-blue-700', 'light'=>'bg-sky-50 dark:bg-sky-950/50', 'icon_c'=>'text-sky-600 dark:text-sky-400',
                ],
                [
                    'icon'=>'eco',       'title'=>'Responsabilidad social',
                    'desc'=>'Nos preocupamos por el impacto de nuestro trabajo. Al digitalizar procesos, ayudamos a las empresas a ser más eficientes, reduciendo el desperdicio y mejorando el entorno laboral.',
                    'items'=>['Sostenibilidad a través de la eficiencia','Ética profesional y trato justo','Compromiso con el talento local'],
                    'accent'=>'from-green-600 to-emerald-700', 'light'=>'bg-green-50 dark:bg-green-950/50', 'icon_c'=>'text-green-600 dark:text-green-400',
                ],
            ] as $i => $v)
            <div class="group relative bg-surface-light dark:bg-surface-dark rounded-2xl p-8 overflow-hidden
                        border border-gray-100 dark:border-white/[0.07]
                        hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">

                <div class="absolute inset-0 bg-gradient-to-br {{ $v['accent'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <div class="relative z-10">
                    <div class="w-14 h-14 {{ $v['light'] }} group-hover:bg-white/20 rounded-2xl flex items-center justify-center mb-6 transition-colors duration-300 shadow-sm">
                        <span class="material-symbols-outlined {{ $v['icon_c'] }} group-hover:text-white text-[26px] transition-colors duration-300"
                              style="font-variation-settings:'FILL' 0,'wght' 300">{{ $v['icon'] }}</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-white mb-3 transition-colors duration-300">
                        {{ $v['title'] }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-white/80 leading-relaxed mb-4 transition-colors duration-300">
                        {{ $v['desc'] }}
                    </p>
                    <ul class="space-y-2">
                        @foreach($v['items'] as $item)
                        <li class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 group-hover:text-white/80 transition-colors duration-300">
                            <span class="material-symbols-outlined text-success group-hover:text-white text-[14px] ms-filled shrink-0">check_circle</span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- QUOTE --}}
<section class="py-20 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center" data-aos="fade-up">
        <span class="material-symbols-outlined text-primary-200 dark:text-primary-900 text-[64px] mb-6 block ms-filled">format_quote</span>
        <blockquote class="text-2xl md:text-3xl font-light italic text-gray-700 dark:text-gray-300 leading-relaxed mb-6 max-w-3xl mx-auto">
            "Nuestros valores no son solo palabras en una pared, son la guía que utilizamos para tomar decisiones cada día"
        </blockquote>
        <div class="flex items-center justify-center gap-3">
            <div class="w-10 h-10 bg-brand rounded-full flex items-center justify-center text-white font-black text-sm">JV</div>
            <div class="text-left">
                <p class="font-black text-gray-900 dark:text-white text-sm">Juan Vásquez</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">CEO & Fundador, JVJ Technology</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-15"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-10"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-black mb-4">¿Compartimos los mismos valores?</h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Entonces trabajemos juntos. Cuéntanos tu proyecto.
        </p>
        <a href="{{ route('public.contacto') }}"
           class="group inline-flex items-center gap-2 px-8 py-4 bg-brand text-white font-black rounded-xl
                  hover:bg-primary-600 active:bg-primary-700 transition-all duration-200
                  shadow-xl shadow-brand/30 hover:shadow-brand/50 hover:scale-[1.02]">
            <span class="material-symbols-outlined text-[20px]">send</span>
            Contáctanos ahora
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>
    </div>
</section>

@endsection
