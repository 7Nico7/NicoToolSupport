@extends('layouts.public')
@section('title', 'Nosotros — JVJ Technology')
@section('meta_description', 'Conoce la historia, el equipo y la metodología de JVJ Technology. Desarrollamos ERP y apps móviles escuchando primero a las personas.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')">
    </div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">business</span>
                Nuestra empresa
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Sobre<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">JVJ Technology</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Conoce la historia, el equipo y la metodología detrás de cada proyecto que construimos.
            </p>
        </div>
    </div>
</section>

{{-- HISTORIA --}}
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <span class="inline-block text-brand dark:text-primary-400 text-xs font-black uppercase tracking-widest mb-3">
                    Nuestra historia
                </span>
                <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                    Un pequeño equipo<br>con grandes sueños
                </h2>
                <div class="space-y-4 text-gray-600 dark:text-gray-400 leading-relaxed text-[15px]">
                    <p>JVJ Technology nació de una idea simple pero poderosa:
                        <strong class="text-gray-900 dark:text-white font-semibold">ayudar a las empresas a digitalizar sus procesos</strong>,
                        pero no desde afuera, sino entendiendo cada detalle de su operación diaria.</p>
                    <p>Lo que comenzó como un pequeño equipo de desarrolladores apasionados, hoy tiene el privilegio de trabajar con
                        <strong class="text-gray-900 dark:text-white font-semibold">JCB, Gavsa, JGV</strong>
                        y otras empresas que han confiado en nuestra forma de trabajar.</p>
                    <p><strong class="text-gray-900 dark:text-white font-semibold">Nuestra metodología es lo que nos hace diferentes:</strong>
                        antes de escribir una sola línea de código, nos sentamos con cada trabajador para entender su día a día.</p>
                    <p>Hemos mantenido nuestra esencia desde el primer día:
                        <strong class="text-gray-900 dark:text-white font-semibold">escuchar primero, programar después.</strong></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" data-aos="fade-left">
                <div class="bg-primary-950 dark:bg-gray-900 rounded-2xl p-8 flex flex-col justify-end h-52 relative overflow-hidden border border-white/[0.06]">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand/20 to-transparent"></div>
                    <span class="material-symbols-outlined text-brand text-5xl mb-2 relative z-10"
                          style="font-variation-settings:'FILL' 0,'wght' 200">rocket_launch</span>
                    <p class="text-white font-black text-lg relative z-10 leading-none">Desde cero</p>
                    <p class="text-primary-300/60 text-sm relative z-10 mt-1">Con grandes ambiciones</p>
                </div>
                <div class="bg-brand rounded-2xl p-8 flex flex-col justify-end h-52 mt-8 relative overflow-hidden shadow-xl shadow-brand/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <span class="material-symbols-outlined text-white text-5xl mb-2 relative z-10"
                          style="font-variation-settings:'FILL' 0,'wght' 200">groups</span>
                    <p class="text-white font-black text-lg relative z-10 leading-none">Hoy</p>
                    <p class="text-primary-200 text-sm relative z-10 mt-1">Con clientes líderes</p>
                </div>
                <div class="col-span-2 bg-surface-light dark:bg-surface-dark rounded-2xl p-5 border border-gray-100 dark:border-white/[0.07]">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 text-center mb-3">
                        Clientes que confían en nosotros
                    </p>
                    <div class="flex justify-around items-center">
                        @foreach([['JCB','text-brand dark:text-primary-400'],['GAVSA','text-gray-700 dark:text-gray-300'],['JGV','text-brand dark:text-primary-400']] as [$name,$col])
                        <div class="text-center">
                            <span class="text-xl font-black {{ $col }}">{{ $name }}</span>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Cliente</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- METODOLOGÍA 5 PASOS --}}
<section class="py-24 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Cómo trabajamos
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">
                Nuestra metodología en 5 pasos
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Desde la primera reunión hasta que el sistema está en marcha, te acompañamos en cada etapa
            </p>
        </div>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach([
                ['num'=>'01','icon'=>'request_quote','title'=>'Cotización',    'desc'=>'Presupuesto claro, justo y sin letra chica desde el inicio.',                          'active'=>false],
                ['num'=>'02','icon'=>'groups',       'title'=>'Inmersión',     'desc'=>'Hablamos con cada trabajador para entender su rol, procesos y retos.',                 'active'=>false],
                ['num'=>'03','icon'=>'code',         'title'=>'Desarrollo',    'desc'=>'Construimos módulo por módulo basado en el flujo real de la empresa.',                 'active'=>false],
                ['num'=>'04','icon'=>'visibility',   'title'=>'Transparencia', 'desc'=>'Avances constantes para que no haya sorpresas en ningún momento.',                    'active'=>false],
                ['num'=>'05','icon'=>'school',       'title'=>'Capacitación',  'desc'=>'Te enseñamos paso a paso hasta que tu equipo domine el sistema.',                     'active'=>true],
            ] as $i => $step)
            <div class="rounded-2xl p-6 text-center border transition-all duration-200
                        {{ $step['active']
                            ? 'bg-brand border-brand shadow-xl shadow-brand/25'
                            : 'bg-background-light dark:bg-background-dark border-gray-100 dark:border-white/[0.07] hover:border-primary-200 dark:hover:border-primary-800 hover:shadow-md' }}"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                <div class="text-[11px] font-black mb-4 {{ $step['active'] ? 'text-primary-200' : 'text-gray-300 dark:text-gray-600' }}">
                    {{ $step['num'] }}
                </div>
                <div class="w-14 h-14 mx-auto rounded-2xl flex items-center justify-center mb-4
                            {{ $step['active'] ? 'bg-white/20' : 'bg-primary-50 dark:bg-primary-950/60' }}">
                    <span class="material-symbols-outlined text-[26px]
                                 {{ $step['active'] ? 'text-white' : 'text-brand dark:text-primary-400' }}"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $step['icon'] }}</span>
                </div>
                <h3 class="font-black mb-2 {{ $step['active'] ? 'text-white' : 'text-gray-900 dark:text-white' }}">
                    {{ $step['title'] }}
                </h3>
                <p class="text-sm leading-relaxed {{ $step['active'] ? 'text-primary-100' : 'text-gray-500 dark:text-gray-400' }}">
                    {{ $step['desc'] }}
                </p>
            </div>
            @endforeach
        </div>

        <p class="text-center mt-10 text-gray-400 dark:text-gray-500 italic text-sm" data-aos="fade-up">
            "No entregamos código y nos vamos. Te acompañamos hasta que tú y tu equipo dominen el sistema."
        </p>
    </div>
</section>

{{-- VALORES (preview) --}}
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Valores
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">Lo que nos define</h2>
        </div>
        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach([
                ['icon'=>'favorite',  'title'=>'Compromiso', 'desc'=>'Nos entregamos por completo a cada proyecto, desde la cotización hasta el último detalle.'],
                ['icon'=>'lightbulb', 'title'=>'Innovación', 'desc'=>'Buscamos siempre las mejores soluciones, adaptadas a la realidad de cada negocio.'],
                ['icon'=>'handshake', 'title'=>'Confianza',  'desc'=>'Construimos relaciones duraderas con transparencia y mostrando avances constantes.'],
                ['icon'=>'star',      'title'=>'Excelencia', 'desc'=>'No nos conformamos con lo mínimo; buscamos la perfección en cada línea de código.'],
            ] as $i => $v)
            <div class="group relative bg-surface-light dark:bg-surface-dark rounded-2xl p-8 overflow-hidden
                        border border-gray-100 dark:border-white/[0.07]
                        hover:border-brand dark:hover:border-brand hover:shadow-xl hover:shadow-brand/15 hover:-translate-y-1.5
                        transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                <div class="absolute inset-0 bg-brand opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary-50 dark:bg-primary-950/60 group-hover:bg-white/20 rounded-2xl flex items-center justify-center mb-6 transition-colors duration-300 shadow-sm">
                        <span class="material-symbols-outlined text-brand dark:text-primary-400 group-hover:text-white text-[26px] transition-colors duration-300"
                              style="font-variation-settings:'FILL' 0,'wght' 300">{{ $v['icon'] }}</span>
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white group-hover:text-white text-lg mb-2 transition-colors duration-300">{{ $v['title'] }}</h3>
                    <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400 group-hover:text-primary-100 transition-colors duration-300">{{ $v['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-10" data-aos="fade-up">
            <a href="{{ route('public.valores') }}"
               class="inline-flex items-center gap-2 text-brand dark:text-primary-400 font-bold hover:gap-3 transition-all duration-200 text-sm">
                Conoce todos nuestros valores
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="py-16 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['val'=>'+3',   'label'=>'Años de experiencia',     'icon'=>'workspace_premium', 'color'=>'text-brand dark:text-primary-400'],
                ['val'=>'100%', 'label'=>'Proyectos personalizados', 'icon'=>'settings',          'color'=>'text-success'],
                ['val'=>'+10',  'label'=>'Proyectos entregados',     'icon'=>'task_alt',          'color'=>'text-warning'],
                ['val'=>'24/7', 'label'=>'Soporte disponible',       'icon'=>'support_agent',     'color'=>'text-info'],
            ] as $i => $stat)
            <div class="flex items-center gap-4 p-5 bg-background-light dark:bg-background-dark rounded-2xl border border-gray-100 dark:border-white/[0.06]"
                 data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">
                <div class="w-12 h-12 bg-primary-50 dark:bg-primary-950/60 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined {{ $stat['color'] }} text-[22px]"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $stat['icon'] }}</span>
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white leading-none">{{ $stat['val'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $stat['label'] }}</p>
                </div>
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
        <h2 class="text-3xl md:text-4xl font-black mb-4">¿Listo para trabajar con nosotros?</h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Cuéntanos tu proyecto y te preparamos una propuesta sin compromiso
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
