@extends('layouts.public')
@section('title', 'Contacto — JVJ Technology')
@section('meta_description', 'Contáctanos para una consultoría gratuita. Desarrollamos ERP y apps móviles a medida para tu empresa.')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-28">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[0.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[0.12]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-[14px]">send</span>
                Hablemos
            </span>
            <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">Contáctanos</h1>
            <p class="text-xl text-primary-200/80 leading-relaxed max-w-2xl">
                Estamos aquí para ayudarte a transformar tu negocio. Cuéntanos tu proyecto.
            </p>
        </div>
    </div>
</section>

{{-- FORMULARIO + INFO --}}
<section class="py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-start">

            {{-- Formulario --}}
            <div data-aos="fade-right">
                <span class="inline-block text-brand dark:text-primary-400 text-xs font-black uppercase tracking-widest mb-3">Escríbenos</span>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-2">Envíanos un mensaje</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-8">Te respondemos en menos de 24 horas hábiles.</p>

                @if(session('success'))
                <div class="flex items-start gap-3 p-4 mb-6 bg-success/10 border border-success/30 rounded-xl text-sm text-success">
                    <span class="material-symbols-outlined text-[18px] mt-0.5 ms-filled shrink-0">check_circle</span>
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                <form action="{{ route('public.contacto.enviar') }}" method="POST" class="space-y-5">
                    @csrf
                    @foreach([
                        ['type'=>'text',  'name'=>'nombre',  'label'=>'Nombre completo',     'placeholder'=>'Ej. María González',       'required'=>true],
                        ['type'=>'email', 'name'=>'email',   'label'=>'Correo electrónico',  'placeholder'=>'correo@empresa.com',       'required'=>true],
                        ['type'=>'tel',   'name'=>'telefono','label'=>'Teléfono',             'placeholder'=>'+52 (993) 000-0000',       'required'=>false],
                    ] as $field)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ $field['label'] }} @if($field['required'])<span class="text-danger">*</span>@endif
                        </label>
                        <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ old($field['name']) }}"
                               placeholder="{{ $field['placeholder'] }}"
                               class="w-full px-4 py-3 rounded-xl border text-sm transition-all duration-200
                                      bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-gray-100
                                      placeholder-gray-400 dark:placeholder-gray-600
                                      border-gray-200 dark:border-white/[0.10]
                                      focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand
                                      @error($field['name']) border-danger focus:ring-danger/30 @enderror">
                        @error($field['name'])
                        <p class="mt-1 text-xs text-danger flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}
                        </p>
                        @enderror
                    </div>
                    @endforeach

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Mensaje <span class="text-danger">*</span>
                        </label>
                        <textarea name="mensaje" rows="5" placeholder="Cuéntanos sobre tu empresa y qué necesitas..."
                                  class="w-full px-4 py-3 rounded-xl border text-sm resize-none transition-all duration-200
                                         bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-gray-100
                                         placeholder-gray-400 dark:placeholder-gray-600
                                         border-gray-200 dark:border-white/[0.10]
                                         focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand
                                         @error('mensaje') border-danger focus:ring-danger/30 @enderror">{{ old('mensaje') }}</textarea>
                        @error('mensaje')
                        <p class="mt-1 text-xs text-danger flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}
                        </p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="group w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold
                                   bg-brand text-white hover:bg-primary-700 active:bg-primary-800
                                   transition-all duration-200 shadow-lg shadow-brand/25 hover:shadow-brand/40">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Enviar mensaje
                        <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </form>
            </div>

            {{-- Info de contacto --}}
            <div data-aos="fade-left">
                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-white/[0.07] p-8 mb-6">
                    <span class="inline-block text-brand dark:text-primary-400 text-xs font-black uppercase tracking-widest mb-3">Información</span>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6">Datos de contacto</h2>
                    <div class="space-y-5">
                        @foreach([
                            ['icon'=>'location_on','title'=>'Dirección', 'lines'=>['C. 3 23, Bonanza, 86030','Villahermosa, Tabasco, MX'],'color'=>'text-brand dark:text-primary-400'],
                            ['icon'=>'call',       'title'=>'Teléfono',  'lines'=>['+52 (993) 123-4567','+52 (993) 123-4568'],             'color'=>'text-success'],
                            ['icon'=>'email',      'title'=>'Email',     'lines'=>['info@jvjtechnology.com','ventas@jvjtechnology.com'],    'color'=>'text-warning'],
                            ['icon'=>'schedule',   'title'=>'Horario',   'lines'=>['Lun–Vie: 9:00 – 17:00','Sáb: 9:00 – 12:00'],          'color'=>'text-info'],
                        ] as $item)
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 bg-primary-50 dark:bg-primary-950/60 rounded-xl flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined {{ $item['color'] }} text-[20px]"
                                      style="font-variation-settings:'FILL' 0,'wght' 300">{{ $item['icon'] }}</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ $item['title'] }}</p>
                                @foreach($item['lines'] as $line)
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $line }}</p>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-white/[0.06]">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4">Síguenos</p>
                        <div class="flex gap-2">
                            @foreach([
                                ['icon'=>'fab fa-facebook-f', 'label'=>'Facebook', 'bg'=>'bg-[#1877f2]'],
                                ['icon'=>'fab fa-twitter',    'label'=>'Twitter',  'bg'=>'bg-[#1da1f2]'],
                                ['icon'=>'fab fa-linkedin-in','label'=>'LinkedIn', 'bg'=>'bg-[#0a66c2]'],
                                ['icon'=>'fab fa-instagram',  'label'=>'Instagram','bg'=>'bg-[#e1306c]'],
                            ] as $sn)
                            <a href="#" aria-label="{{ $sn['label'] }}"
                               class="w-9 h-9 {{ $sn['bg'] }} rounded-lg flex items-center justify-center text-white text-xs
                                      hover:opacity-90 hover:scale-110 transition-all duration-200">
                                <i class="{{ $sn['icon'] }}"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-primary-950 dark:bg-gray-900 rounded-2xl p-6 border border-white/[0.06] relative overflow-hidden">
                    <div class="absolute -top-8 -right-8 w-32 h-32 bg-brand rounded-full blur-2xl opacity-20"></div>
                    <div class="relative z-10">
                        <span class="material-symbols-outlined text-warning text-[28px] mb-3 block ms-filled">star</span>
                        <h3 class="font-black text-white text-lg mb-2">Consultoría sin costo</h3>
                        <p class="text-primary-200/70 text-sm leading-relaxed mb-4">
                            Agenda una videollamada y te mostramos cómo digitalizar tu operación.
                        </p>
                        <a href="tel:+529931234567"
                           class="inline-flex items-center gap-2 text-sm font-bold text-white bg-brand hover:bg-primary-600 px-4 py-2.5 rounded-xl transition-all duration-200">
                            <span class="material-symbols-outlined text-[16px]">call</span>
                            Llamar ahora
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- MAPA --}}
<section class="border-t border-gray-100 dark:border-white/[0.06]">
    <div class="relative h-80 md:h-96">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1200.123456789!2d-92.93919489668433!3d18.003283448627062!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85edd7f53c480461%3A0x7381dbc1fe40743b!2sC.%203%2023%2C%20Bonanza%2C%2086030%20Villahermosa%2C%20Tab.!5e0!3m2!1ses-419!2smx!4v1772029586915!5m2!1ses-419!2smx"
                width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"
                class="grayscale hover:grayscale-0 transition-all duration-500">
        </iframe>
    </div>
</section>

@endsection
