@extends('layouts.public')
@section('title', 'Misión y Visión — JVJ Technology')
@section('meta_description', 'Conoce la misión y visión 2028 de JVJ Technology: ser el socio tecnológico de referencia en Latinoamérica para la transformación digital.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">track_changes</span>
                Propósito
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Misión<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">y Visión</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Nuestro propósito y hacia dónde nos dirigimos como empresa.
            </p>
        </div>
    </div>
</section>

{{-- MISIÓN --}}
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <span class="inline-block text-brand dark:text-primary-400 text-xs font-black uppercase tracking-widest mb-3">Nuestra razón de ser</span>
                <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">Misión</h2>
                <p class="text-gray-600 dark:text-gray-400 text-[15px] leading-relaxed mb-8">
                    <strong class="text-gray-900 dark:text-white font-semibold">Digitalizar la esencia de cada negocio.</strong>
                    Nos enfocamos en comprender a fondo las operaciones de nuestros clientes, desde la cotización inicial hasta los procesos diarios de cada equipo. A través de un análisis detallado con los trabajadores, desarrollamos sistemas ERP y aplicaciones a la medida que automatizan tareas, integran información y potencian la productividad.
                </p>

                <div class="bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-white/[0.07] rounded-2xl p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4">Nuestro método</p>
                    <div class="space-y-3">
                        @foreach([
                            ['icon'=>'request_quote','step'=>'1','title'=>'Cotización precisa',  'desc'=>'Presupuesto claro y justo antes de empezar.'],
                            ['icon'=>'groups',       'step'=>'2','title'=>'Inmersión total',     'desc'=>'Hablamos con cada trabajador para entender su día a día.'],
                            ['icon'=>'code',         'step'=>'3','title'=>'Desarrollo modular',  'desc'=>'Construimos pieza por pieza basado en el flujo real.'],
                            ['icon'=>'visibility',   'step'=>'4','title'=>'Transparencia total', 'desc'=>'Avances constantes para que no haya sorpresas.'],
                        ] as $step)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-white text-[15px]"
                                      style="font-variation-settings:'FILL' 0,'wght' 300">{{ $step['icon'] }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    <span class="text-primary-400 text-[10px] font-black mr-1">0{{ $step['step'] }}</span>
                                    {{ $step['title'] }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div data-aos="fade-left">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://placehold.co/600x420/1e3a8a/ffffff?text=Análisis+de+Procesos"
                         alt="Análisis de Procesos JVJ" class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-950/40 to-transparent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VISIÓN --}}
<section class="py-24 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="order-2 md:order-1" data-aos="fade-right">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://placehold.co/600x420/172554/ffffff?text=Impacto+Regional"
                         alt="Visión de Expansión JVJ" class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-950/40 to-transparent"></div>
                </div>
            </div>

            <div class="order-1 md:order-2" data-aos="fade-left">
                <span class="inline-block text-brand dark:text-primary-400 text-xs font-black uppercase tracking-widest mb-3">Hacia dónde vamos</span>
                <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">Visión 2028</h2>
                <p class="text-gray-600 dark:text-gray-400 text-[15px] leading-relaxed mb-8">
                    Ser el socio tecnológico de referencia en Latinoamérica para empresas que buscan una
                    <strong class="text-gray-900 dark:text-white font-semibold">transformación digital profunda y humana.</strong>
                    Aspiramos a ser reconocidos no solo por nuestra tecnología, sino por nuestra capacidad de entender y potenciar el talento de las personas que hacen funcionar un negocio.
                </p>

                <div class="bg-background-light dark:bg-background-dark border border-gray-100 dark:border-white/[0.07] rounded-2xl p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4">Metas estratégicas</p>
                    <div class="space-y-3">
                        @foreach([
                            ['icon'=>'group',         'text'=>'Consolidar un equipo de más de 50 expertos en análisis y desarrollo.'],
                            ['icon'=>'auto_awesome',  'text'=>'Crear la metodología JVJ Way para la digitalización de negocios.'],
                            ['icon'=>'public',        'text'=>'Presencia en 5 países de Latinoamérica transformando cadenas productivas.'],
                        ] as $meta)
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-brand dark:text-primary-400 text-[18px] shrink-0 mt-0.5"
                                  style="font-variation-settings:'FILL' 0,'wght' 300">{{ $meta['icon'] }}</span>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $meta['text'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VALORES QUICK --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-12" data-aos="fade-up">Nuestros Valores</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            @foreach([
                ['icon'=>'favorite',  'label'=>'Compromiso'],
                ['icon'=>'lightbulb', 'label'=>'Innovación'],
                ['icon'=>'handshake', 'label'=>'Confianza'],
                ['icon'=>'star',      'label'=>'Excelencia'],
                ['icon'=>'groups',    'label'=>'Trabajo en equipo'],
            ] as $i => $v)
            <div class="group bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-white/[0.07]
                        hover:border-brand dark:hover:border-brand hover:shadow-lg
                        rounded-2xl p-5 text-center transition-all duration-200"
                 data-aos="zoom-in" data-aos-delay="{{ $i * 60 }}">
                <div class="w-12 h-12 bg-primary-50 dark:bg-primary-950/60 group-hover:bg-brand rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors duration-200">
                    <span class="material-symbols-outlined text-brand dark:text-primary-400 group-hover:text-white text-[20px] transition-colors duration-200"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $v['icon'] }}</span>
                </div>
                <p class="text-sm font-black text-gray-900 dark:text-white group-hover:text-brand dark:group-hover:text-primary-400 transition-colors">{{ $v['label'] }}</p>
            </div>
            @endforeach
        </div>
        <div class="mt-10" data-aos="fade-up">
            <a href="{{ route('public.valores') }}"
               class="inline-flex items-center gap-2 text-brand dark:text-primary-400 font-bold hover:gap-3 transition-all duration-200 text-sm">
                Conoce todos nuestros valores
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

@endsection
