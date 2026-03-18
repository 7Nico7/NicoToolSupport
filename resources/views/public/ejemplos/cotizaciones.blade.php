
@extends('layouts.public')
@section('title', 'Demo — Sistema de Cotizaciones ERP')

@section('content')


<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')">
    </div>
    <div class="absolute -top-24 -right-24 w-80 h-80 bg-brand rounded-full blur-3xl opacity-[0.16]"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-primary-700 rounded-full blur-3xl opacity-[0.10]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20
                         text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">request_quote</span>
                Demo interactivo
            </span>
            <h1 class="text-4xl md:text-5xl font-black leading-tight mb-5">
                Demo: Sistema de<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">Cotizaciones</span>
            </h1>
            <p class="text-lg text-primary-200/80 leading-relaxed max-w-2xl">Prueba nuestro módulo de cotizaciones en acción. Totalmente funcional, interactivo y personalizable.</p>
        </div>
    </div>
</section>


<section class="py-8 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


        <div class="flex flex-wrap items-center justify-between gap-3
                    bg-surface-light dark:bg-surface-dark
                    border border-gray-200 dark:border-white/[0.08]
                    rounded-t-2xl px-5 py-3">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-success animate-pulse shrink-0"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                    Demo en vivo — sistema parcialmente funcional
                </span>
            </div>
            <button onclick="openDemo()"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold
                           text-gray-500 dark:text-gray-400 hover:text-brand dark:hover:text-primary-400
                           px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.06]
                           transition-all duration-200">
                <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                Abrir en nueva pestaña
            </button>
        </div>


        <div class="rounded-b-2xl overflow-hidden border-x border-b
                    border-gray-200 dark:border-white/[0.08]
                    shadow-2xl shadow-black/[0.10]"
             style="height: 720px;">
            <iframe
                src="{{ route('demo.cotizaciones') }}"
                frameborder="0"
                style="width:100%;height:100%;border:none;"
                title="Demo de Cotizaciones"
                allow="fullscreen"
                id="demoIframe">
            </iframe>
        </div>


        <div class="mt-6 bg-surface-light dark:bg-surface-dark rounded-2xl p-6
                    border border-gray-100 dark:border-white/[0.07]"
             data-aos="fade-up">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-brand dark:text-primary-400 text-[18px]"
                      style="font-variation-settings:'FILL' 0,'wght' 300">info</span>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
                    Características del demo
                </h3>
            </div>
            <ul class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <li class="flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-success text-[15px] ms-filled shrink-0 mt-0.5">check_circle</span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            <strong class="font-semibold text-gray-900 dark:text-white">Tabla interactiva:</strong> Ordena, filtra y busca cotizaciones en tiempo real
                        </span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-success text-[15px] ms-filled shrink-0 mt-0.5">check_circle</span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            <strong class="font-semibold text-gray-900 dark:text-white">Gestión de columnas:</strong> Muestra u oculta columnas según tu preferencia
                        </span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-success text-[15px] ms-filled shrink-0 mt-0.5">check_circle</span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            <strong class="font-semibold text-gray-900 dark:text-white">Exportar a Excel:</strong> Descarga los datos en formato .xlsx con un clic
                        </span>
                    </li>
            </ul>
        </div>
    </div>
</section>


<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-20">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.15]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.10]"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-black mb-4">
            ¿Listo para implementarlo en tu empresa?
        </h2>
        <p class="text-lg text-primary-200/80 mb-10 max-w-xl mx-auto">
            Este es solo un ejemplo. Nosotros adaptamos cada módulo a tus procesos reales.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('public.contacto') }}"
               class="group inline-flex items-center gap-2 px-8 py-4 bg-brand text-white font-black rounded-xl
                      hover:bg-primary-600 active:bg-primary-700 transition-all duration-200
                      shadow-xl shadow-brand/30 hover:shadow-brand/50 hover:scale-[1.02]">
                <span class="material-symbols-outlined text-[20px]">send</span>
                Solicitar cotización
                <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
            <a href="{{ route('public.nosotros') }}"
               class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-white
                      border-2 border-white/30 hover:border-white hover:bg-white/10
                      transition-all duration-200">
                <span class="material-symbols-outlined text-[20px]">groups</span>
                Conoce nuestro método
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    function openDemo() {
        window.open('{{ route('demo.cotizaciones') }}', '_blank', 'width=1280,height=820');
    }
</script>
@endpush
