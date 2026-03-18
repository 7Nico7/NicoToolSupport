{{-- file: resources/views/public/servicios/movil.blade.php --}}
@extends('layouts.public')
@section('title', 'Desarrollo de Apps Móviles — JVJ Technology')
@section('meta_description', 'Aplicaciones nativas e híbridas para iOS y Android. Captura de evidencias, ventas en campo, inventario móvil y más, sincronizados con tu ERP.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">smartphone</span>
                Servicio
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                Apps<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-300">Móviles</span>
            </h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Aplicaciones nativas e híbridas para iOS y Android. Desde herramientas específicas hasta sistemas completos en el bolsillo.
            </p>
        </div>
    </div>
</section>

{{-- QUÉ HACEMOS --}}
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <span class="inline-block text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-widest mb-3">Movilidad empresarial</span>
                <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                    Tu negocio<br>en el bolsillo
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-[15px] leading-relaxed mb-8">
                    Desarrollamos apps móviles que resuelven necesidades específicas: captura de evidencias, reportes en campo, consulta de información, o incluso
                    <strong class="text-gray-900 dark:text-white font-semibold">sistemas completos con operación offline.</strong>
                </p>
                <div class="space-y-4">
                    @foreach([
                        ['icon'=>'photo_camera',  'title'=>'Captura de evidencias', 'desc'=>'Fotos, firmas, geolocalización y formularios desde campo'],
                        ['icon'=>'bar_chart',     'title'=>'Reportes y dashboards', 'desc'=>'Indicadores clave siempre a la mano en tu dispositivo'],
                        ['icon'=>'wifi_off',      'title'=>'Modo offline',          'desc'=>'Trabaja sin conexión y sincroniza automáticamente al reconectar'],
                        ['icon'=>'qr_code_scanner','title'=>'Escaneo de códigos',   'desc'=>'QR, códigos de barras e integración con hardware externo'],
                    ] as $feat)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[17px]"
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
                    <img src="https://placehold.co/600x420/064e3b/ffffff?text=Mobile+Development"
                         alt="Desarrollo Móvil JVJ" class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/40 to-transparent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TIPOS DE APPS --}}
<section class="py-20 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Soluciones
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">
                Soluciones móviles para cada necesidad
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Desde herramientas específicas hasta sistemas empresariales completos
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['icon'=>'fact_check',    'title'=>'Captura de evidencias', 'desc'=>'Inspectores y supervisores en campo. Fotos, firmas, ubicación y formularios con o sin conexión.'],
                ['icon'=>'bar_chart',     'title'=>'Reportes móviles',      'desc'=>'Consulta indicadores, ventas y productividad desde tu dispositivo en todo momento.'],
                ['icon'=>'point_of_sale', 'title'=>'Ventas en campo',       'desc'=>'Toma pedidos, cotiza, consulta inventario y cierra ventas. Sincronización en tiempo real.'],
                ['icon'=>'local_shipping','title'=>'Logística y entregas',  'desc'=>'Rastreo de rutas, confirmación de entregas, firma digital y evidencia fotográfica.'],
                ['icon'=>'inventory_2',   'title'=>'Inventario móvil',      'desc'=>'Conteos cíclicos, recepciones, ajustes y consulta de existencias con escaneo de códigos.'],
                ['icon'=>'apps',          'title'=>'Sistema completo',      'desc'=>'¿Necesitas todo tu ERP en móvil? Desarrollamos versiones móviles completas de tu sistema.'],
            ] as $i => $app)
            <div class="group bg-background-light dark:bg-background-dark rounded-2xl p-6
                        border border-gray-100 dark:border-white/[0.07]
                        hover:border-emerald-500 dark:hover:border-emerald-600 hover:shadow-lg hover:-translate-y-1
                        transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 group-hover:bg-emerald-600 rounded-xl
                            flex items-center justify-center mb-4 transition-colors duration-300">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 group-hover:text-white text-[22px] transition-colors duration-300"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $app['icon'] }}</span>
                </div>
                <h3 class="font-black text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
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
            <span class="inline-block bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                Stack
            </span>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-3">Tecnologías que dominamos</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                Elegimos la mejor tecnología para cada tipo de app
            </p>
        </div>
        <div class="flex flex-wrap justify-center gap-3" data-aos="fade-up">
            @foreach(['React Native','Flutter','iOS (Swift)','Android (Kotlin)','PWA','Firebase'] as $tech)
            <span class="px-5 py-2.5 rounded-xl text-sm font-bold
                         bg-surface-light dark:bg-surface-dark
                         text-gray-700 dark:text-gray-300
                         border border-gray-200 dark:border-white/[0.10]
                         hover:border-emerald-500 hover:text-emerald-600 dark:hover:border-emerald-500 dark:hover:text-emerald-400
                         transition-all duration-200">{{ $tech }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- CROSSLINK WEB --}}
<section class="py-16 bg-surface-light dark:bg-surface-dark border-y border-gray-100 dark:border-white/[0.06]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center" data-aos="fade-up">
        <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[28px]"
                  style="font-variation-settings:'FILL' 0,'wght' 300">cloud_sync</span>
        </div>
        <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-4">¿Ya tienes un sistema web?</h2>
        <p class="text-gray-500 dark:text-gray-400 text-[15px] leading-relaxed mb-8">
            Podemos conectar tu app móvil con tu sistema web existente. Sincronización en tiempo real, misma base de datos, misma lógica de negocio. Tu información siempre actualizada en todas las plataformas.
        </p>
        <a href="{{ route('public.servicios.desarrollo') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold
                  bg-background-light dark:bg-background-dark border border-gray-200 dark:border-white/[0.10]
                  text-gray-700 dark:text-gray-300 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400
                  transition-all duration-200">
            <span class="material-symbols-outlined text-[18px]">storage</span>
            Conoce desarrollo web
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-15"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-700 rounded-full blur-3xl opacity-10"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-black mb-4">¿Necesitas una app móvil?</h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Cuéntanos qué necesitas y te ayudamos a hacerla realidad.
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
