{{-- file: resources/views/public/servicios/desarrollo.blade.php --}}
@extends('layouts.public')

@section('title', 'Desarrollo Web a Medida — JVJ Technology')
@section('meta_description',
    'Creamos sistemas web empresariales, ERPs, portales y plataformas digitales personalizadas.
    Analizamos tu operación y construimos lo que realmente necesitas.')

@section('content')

{{-- ─────────────────────────────────────────────────────────
     ESTILOS (sólo lo que Tailwind no puede manejar)
───────────────────────────────────────────────────────── --}}
<style>
/* ── Fuentes originales del proyecto ── */
@import url('https://fonts.googleapis.com/css2?family=Archivo:wght@300;400;500;600;700&display=swap');

:root {
    --ease-out:    cubic-bezier(.16,1,.3,1);
    --ease-spring: cubic-bezier(.34,1.56,.64,1);
}

.font-clash  { font-family: "Clash Display", sans-serif; }
.font-archivo{ font-family: "Archivo", sans-serif; }

/* ── Evitar scroll horizontal causado por los slides laterales ── */
html { overflow-x: hidden; }
.slider-section-wrap { overflow: hidden; }

/* ── Hero ── */
.hero-counter {
    position: absolute;
    right: -3rem;
    top: 50%;
    transform: translateY(-50%);
    font-family: "Clash Display", sans-serif;
    font-size: clamp(12rem, 20vw, 22rem);
    font-weight: 800;
    line-height: 1;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255,255,255,.04);
    user-select: none;
    pointer-events: none;
    letter-spacing: -.05em;
}

.hero-scroll-line {
    width: 1px;
    height: 48px;
    background: linear-gradient(to bottom, rgba(255,255,255,.35), transparent);
    animation: scrollPulse 1.8s ease-in-out infinite;
}
@keyframes scrollPulse {
    0%      { transform: scaleY(0); transform-origin: top; }
    50%     { transform: scaleY(1); transform-origin: top; }
    50.001% { transform-origin: bottom; }
    100%    { transform: scaleY(0); transform-origin: bottom; }
}

/* ── Marquee ── */
.marquee-track {
    display: flex;
    gap: 2rem;
    white-space: nowrap;
    animation: marquee 30s linear infinite;
    will-change: transform;
}
.marquee-track:hover { animation-play-state: paused; }
@keyframes marquee {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

/* ── Feature rows ── */
.feature-row {
    display: flex;
    align-items: flex-start;
    gap: .9rem;
    padding: 1.1rem 0;
    border-top: 1px solid;
    transition: padding-left .2s var(--ease-out);
}
.feature-row:hover { padding-left: .35rem; }

/* ── App cards (fill-up hover) ── */
.app-card {
    position: relative;
    overflow: hidden;
    cursor: default;
}
.app-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: #2563eb;
    transform: translateY(102%);
    transition: transform .35s var(--ease-out);
    z-index: 0;
}
.app-card:hover::before { transform: translateY(0); }
.app-card > * { position: relative; z-index: 1; }

/* ── Tech pills ── */
.tech-pill {
    position: relative;
    overflow: hidden;
    cursor: default;
    transition: color .2s, border-color .2s, transform .2s var(--ease-spring);
}
.tech-pill::after {
    content: '';
    position: absolute;
    inset: 0;
    background: #2563eb;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .22s var(--ease-out);
    z-index: -1;
}
.tech-pill:hover { transform: translateY(-2px); }
.tech-pill:hover::after { transform: scaleX(1); }

/* ── Reveal ── */
[data-reveal] {
    opacity: 0;
    transform: translateY(22px);
    transition: opacity .6s var(--ease-out), transform .6s var(--ease-out);
}
[data-reveal].is-visible  { opacity: 1; transform: none; }
[data-reveal-delay="1"]   { transition-delay: .1s; }
[data-reveal-delay="2"]   { transition-delay: .2s; }
[data-reveal-delay="3"]   { transition-delay: .3s; }
[data-reveal-delay="4"]   { transition-delay: .4s; }

/* ─── Slider 3D ─── */
.slider-3d-container {
    --slide-width: min(25vw, 300px);
    --slide-aspect: 2 / 3;
    --slide-transition-duration: 800ms;
    --slide-transition-easing: cubic-bezier(.16,1,.3,1);
    --font-archivo: "Archivo", sans-serif;
    --font-clash-display: "Clash Display", sans-serif;
    --slide-gap: 1.07;
}
@media (max-width: 768px) {
    .slider-3d-container { --slide-width: 65vw; --slide-aspect: 3/4; --slide-gap: 1.15; }
}
.slider-3d-container * { box-sizing: border-box; margin: 0; padding: 0; }
.slider-3d-container button { border: none; background: none; cursor: pointer; -webkit-tap-highlight-color: transparent; }
.slider-3d-container button:focus { outline: none; }

.slider-3d-container .slider {
    width: 100%; height: 100%; display: flex;
    align-items: center; justify-content: center;
    position: relative; container-type: size;
}
.slider-3d-container .slider--btn {
    display: inline-flex; justify-content: center; align-items: center;
    opacity: .65;
    transition: opacity .2s, background .2s, transform .2s var(--ease-spring), border-color .2s;
    z-index: 999; position: absolute; top: 50%; transform: translateY(-50%);
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 3px; padding: .65rem;
}
.slider-3d-container .slider--btn:hover {
    opacity: 1; background: #2563eb; border-color: #2563eb;
    transform: translateY(-50%) scale(1.06);
}
.slider-3d-container .slider--btn__prev { left: 14px; }
.slider-3d-container .slider--btn__next { right: 14px; }
.slider-3d-container .slider--btn svg   { width: 20px; height: 20px; stroke: white; }
@media (max-width:480px) {
    .slider-3d-container .slider--btn { opacity:.3; top:auto; bottom:28px; transform:none; }
}

.slider-3d-container .slides__wrapper { width:100%; height:100%; display:grid; place-items:center; }
.slider-3d-container .slides__wrapper > * { grid-area: 1/-1; }
.slider-3d-container .slides,
.slider-3d-container .slides--infos { width:100%; height:100%; pointer-events:none; display:grid; place-items:center; }
.slider-3d-container .slides > *,
.slider-3d-container .slides--infos > * { grid-area: 1/-1; }

.slider-3d-container .slide {
    --slide-tx:0px; --slide-ty:0vh; --padding:0px;
    width:var(--slide-width); height:auto; aspect-ratio:var(--slide-aspect);
    user-select:none; perspective:800px;
    transform:perspective(1000px) translate3d(var(--slide-tx),var(--slide-ty),var(--slide-tz,0)) rotateY(var(--slide-rotY)) scale(var(--slide-scale));
    transition:transform var(--slide-transition-duration) var(--slide-transition-easing);
    will-change:transform;
}
.slider-3d-container .slide[data-current]  { --slide-scale:1.2; --slide-tz:0px; --slide-tx:0px; --slide-rotY:0; pointer-events:auto; z-index:20; }
.slider-3d-container .slide[data-next]     { --slide-tx:calc(1*var(--slide-width)*var(--slide-gap)); --slide-rotY:-45deg; --slide-scale:1; pointer-events:none; z-index:10; }
.slider-3d-container .slide[data-previous] { --slide-tx:calc(-1*var(--slide-width)*var(--slide-gap)); --slide-rotY:45deg; --slide-scale:1; pointer-events:none; z-index:10; }
@media (max-width:768px) {
    .slider-3d-container .slide[data-next]     { --slide-rotY:-25deg; }
    .slider-3d-container .slide[data-previous] { --slide-rotY:25deg; }
    .slider-3d-container .slide[data-current]  { --slide-scale:1.1; }
}
.slider-3d-container .slide[data-current] .slide--image      { filter:brightness(.8); }
.slider-3d-container .slide:not([data-current]) .slide--image { filter:brightness(.45) saturate(.7); }

.slider-3d-container .slide__inner {
    --rotX:0; --rotY:0; --bgPosX:0%; --bgPosY:0%;
    position:relative; left:calc(var(--padding)/2); top:calc(var(--padding)/2);
    width:calc(100% - var(--padding)); height:calc(100% - var(--padding));
    transform-style:preserve-3d; transform:rotateX(var(--rotX)) rotateY(var(--rotY));
}
.slider-3d-container .slide--image__wrapper {
    position:relative; width:100%; height:100%;
    overflow:hidden; border-radius:4px;
    box-shadow:0 28px 56px rgba(0,0,0,.5);
}
.slider-3d-container .slide--image {
    width:100%; height:100%; position:absolute; top:50%; left:50%; object-fit:cover;
    transform:translate(-50%,-50%) scale(1.25) translate3d(var(--bgPosX),var(--bgPosY),0);
    transition:filter var(--slide-transition-duration) var(--slide-transition-easing);
}
.slider-3d-container .slide__bg {
    position:fixed; inset:-20%;
    background-image:var(--bg); background-size:cover; background-position:center;
    z-index:-1; pointer-events:none;
    transition:opacity var(--slide-transition-duration) ease, transform var(--slide-transition-duration) ease;
}
.slider-3d-container .slide__bg::before {
    content:''; position:absolute; inset:0;
    background:rgba(0,0,0,.82);
    backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);
}
.slider-3d-container .slide__bg:not([data-current]) { opacity:0; }
.slider-3d-container .slide__bg[data-previous] { transform:translateX(-10%); }
.slider-3d-container .slide__bg[data-next]     { transform:translateX(10%); }

.slider-3d-container .slide-info {
    --padding:0px; position:relative;
    width:var(--slide-width); height:100%; aspect-ratio:var(--slide-aspect);
    user-select:none; perspective:800px; z-index:100; container-type:size;
}
.slider-3d-container .slide-info[data-current] .slide-info--text span      { opacity:1; transform:translate3d(0,0,0); transition-delay:250ms; }
.slider-3d-container .slide-info:not([data-current]) .slide-info--text span { opacity:0; transform:translate3d(0,100%,0); transition-delay:0ms; }
.slider-3d-container .slide-info__inner { position:relative; width:100%; height:100%; transform-style:preserve-3d; transform:rotateX(var(--rotX)) rotateY(var(--rotY)); }
.slider-3d-container .slide-info--text__wrapper {
    --z-offset:45px; position:absolute; height:fit-content;
    left:-15%; bottom:15%; transform:translateZ(var(--z-offset)); z-index:2; pointer-events:none;
}
@media (max-width:768px) {
    .slider-3d-container .slide-info--text__wrapper { left:0; bottom:10%; width:100%; text-align:center; padding:0 1rem; }
    .slider-3d-container .slide-info--text[data-subtitle]    { margin-left:0 !important; }
    .slider-3d-container .slide-info--text[data-description] { margin-left:0 !important; }
}
.slider-3d-container .slide-info--text { font-family:var(--font-clash-display); color:#fff; overflow:hidden; }
.slider-3d-container .slide-info--text span {
    display:block; white-space:nowrap;
    transition:var(--slide-transition-duration) var(--slide-transition-easing);
    transition-property:opacity,transform;
}
.slider-3d-container .slide-info--text[data-title]       { font-size:clamp(2rem,8cqw,4rem); font-weight:800; text-transform:uppercase; }
.slider-3d-container .slide-info--text[data-subtitle]    { font-size:clamp(1rem,4cqw,2rem); font-weight:600; color:#93c5fd; margin-left:1rem; }
.slider-3d-container .slide-info--text[data-description] { font-size:clamp(.8rem,2.5cqw,1.2rem); font-family:var(--font-archivo); font-weight:300; margin-left:.5rem; margin-top:10px; }
</style>

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white"
         style="min-height:100svh;display:flex;align-items:center;">

    <div class="absolute inset-0 opacity-[.04]"
         style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')">
    </div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[.18]"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[.12]"></div>

    <div class="hero-counter" aria-hidden="true">01</div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-24 w-full">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-sm
                        bg-white/10 border border-white/20
                        text-primary-300 text-xs font-bold uppercase tracking-widest mb-8 font-clash"
                 data-reveal>
                <span class="material-symbols-outlined text-[14px]">code</span>
                Servicio especializado
            </div>

            <h1 class="font-clash font-black leading-[.96] tracking-tight text-white mb-6"
                style="font-size:clamp(3.5rem,9vw,7.5rem);"
                data-reveal data-reveal-delay="1">
                Desarrollo<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">
                    Web a Medida
                </span>
            </h1>

            <p class="font-archivo text-primary-200/70 leading-relaxed max-w-2xl mb-10"
               style="font-size:clamp(1rem,1.4vw,1.15rem);font-weight:300;"
               data-reveal data-reveal-delay="2">
                Sistemas empresariales, ERPs, portales y plataformas digitales
                construidas exactamente para tu operación. Sin atajos, sin genéricos.
            </p>

            <a href="{{ route('public.contacto') }}"
               class="inline-flex items-center gap-2 px-7 py-3.5 rounded-sm
                      bg-brand hover:bg-primary-600 active:bg-primary-700 text-white
                      font-clash font-bold text-sm tracking-wider uppercase
                      shadow-xl shadow-brand/30 hover:shadow-brand/50
                      hover:-translate-y-0.5 hover:scale-[1.02] transition-all duration-200"
               data-reveal data-reveal-delay="3">
                <span class="material-symbols-outlined text-[18px]">send</span>
                Solicitar propuesta
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>

    {{-- Stats flotantes --}}
    <div class="absolute right-8 bottom-8 hidden lg:flex gap-3 z-10">
        @foreach([['80+','Proyectos'],['8+','Años'],['99%','Uptime']] as $s)
        <div class="bg-white/[.05] border border-white/10 rounded-sm px-5 py-3 backdrop-blur-sm">
            <strong class="font-clash font-black text-2xl text-white block leading-none tracking-tight">{{ $s[0] }}</strong>
            <span class="font-archivo text-[.68rem] text-white/40 uppercase tracking-widest mt-1 block">{{ $s[1] }}</span>
        </div>
        @endforeach
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2
                text-white/25 font-clash text-[.6rem] font-bold tracking-widest uppercase"
         aria-hidden="true">
        <span>Scroll</span>
        <div class="hero-scroll-line"></div>
    </div>
</section>

{{-- ═══════════════ MARQUEE ═══════════════ --}}
<div class="overflow-hidden bg-brand py-3" aria-hidden="true">
    <div class="marquee-track">
        @php
            $items = ['Sistemas ERP','Desarrollo a medida','E-commerce','APIs REST','CRM','Dashboards','Facturación electrónica','Cloud','Integraciones'];
            $rep   = array_merge($items,$items,$items,$items);
        @endphp
        @foreach($rep as $item)
        <span class="font-clash font-bold text-[.72rem] uppercase tracking-[.18em] text-white/80 shrink-0
                     flex items-center gap-5">
            {{ $item }}
            <span class="text-white/30 text-[.45rem]">✦</span>
        </span>
        @endforeach
    </div>
</div>

{{-- ═══════════════ QUÉ HACEMOS ═══════════════ --}}
<section class="slider-section-wrap py-28 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-20 items-center">

            <div>
                <div class="flex items-center gap-3 mb-4" data-reveal>
                    <span class="block w-8 h-0.5 bg-brand dark:bg-primary-400 rounded"></span>
                    <span class="font-clash font-bold text-[.65rem] uppercase tracking-[.2em]
                                 text-brand dark:text-primary-400">
                        Por qué elegirnos
                    </span>
                </div>

                <h2 class="font-clash font-black text-gray-900 dark:text-white
                           leading-[.97] tracking-tight mb-6"
                    style="font-size:clamp(2.2rem,5vw,3.8rem);"
                    data-reveal data-reveal-delay="1">
                    Sistemas que<br>
                    <span class="text-transparent bg-clip-text
                                 bg-gradient-to-r from-primary-600 to-primary-500
                                 dark:from-primary-300 dark:to-primary-400">
                        impulsan
                    </span><br>
                    tu negocio
                </h2>

                <p class="font-archivo text-gray-500 dark:text-gray-400 leading-relaxed mb-8"
                   style="font-size:.97rem;font-weight:300;"
                   data-reveal data-reveal-delay="2">
                    Desarrollamos aplicaciones web robustas, escalables y seguras, adaptadas exactamente a tus procesos.
                    <strong class="text-gray-900 dark:text-white font-semibold">No vendemos software genérico:</strong>
                    analizamos tu operación y construimos lo que realmente necesitas.
                </p>

                <div data-reveal data-reveal-delay="3">
                    @foreach ([
                        ['icon'=>'architecture',         'title'=>'Arquitectura robusta',          'desc'=>'Sistemas preparados para crecer con tu negocio'],
                        ['icon'=>'lock',                 'title'=>'Seguridad empresarial',          'desc'=>'Protección de datos y roles de usuario por perfil'],
                        ['icon'=>'public',               'title'=>'Acceso universal',               'desc'=>'Sin instalaciones, siempre actualizado automáticamente'],
                        ['icon'=>'integration_instructions','title'=>'API e integraciones',         'desc'=>'Conéctate con cualquier sistema o plataforma existente'],
                    ] as $feat)
                    <div class="feature-row border-gray-100 dark:border-white/[.07]">
                        <div class="w-9 h-9 bg-primary-50 dark:bg-primary-950/60
                                    flex items-center justify-center rounded-sm shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-brand dark:text-primary-400 text-[17px]"
                                  style="font-variation-settings:'FILL' 0,'wght' 300">{{ $feat['icon'] }}</span>
                        </div>
                        <div>
                            <p class="font-clash font-bold text-gray-900 dark:text-white text-sm">{{ $feat['title'] }}</p>
                            <p class="font-archivo text-gray-500 dark:text-gray-400 text-xs mt-0.5" style="font-weight:300;">{{ $feat['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Slider 3D --}}
            <div class="flex justify-center" data-reveal data-reveal-delay="2">
                <div class="slider-3d-container" style="width:100%;max-width:500px;min-height:500px;">
                    <div class="slider" style="width:100%;height:100%;">
                        <button class="slider--btn slider--btn__prev" aria-label="Anterior">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <div class="slides__wrapper">
                            <div class="slides">
                                <div class="slide" data-current>
                                    <div class="slide__inner"><div class="slide--image__wrapper">
                                        <img class="slide--image" src="{{ asset('img/fleetnet.png') }}" alt="Arquitectura robusta"/>
                                    </div></div>
                                </div>
                                <div class="slide__bg" style="--bg:url({{ asset('img/fleetnet.png') }})" data-current></div>

                                <div class="slide" data-next>
                                    <div class="slide__inner"><div class="slide--image__wrapper">
                                        <img class="slide--image" src="{{ asset('img/image.png') }}" alt="Seguridad empresarial"/>
                                    </div></div>
                                </div>
                                <div class="slide__bg" style="--bg:url({{ asset('img/image.png') }})" data-next></div>

                                <div class="slide" data-previous>
                                    <div class="slide__inner"><div class="slide--image__wrapper">
                                        <img class="slide--image" src="https://placehold.co/600x420/1e3a8a/ffffff?text=Integraciones+API" alt="API e integraciones"/>
                                    </div></div>
                                </div>
                                <div class="slide__bg" style="--bg:url(https://placehold.co/600x420/1e3a8a/ffffff?text=Integraciones+API)" data-previous></div>
                            </div>
                            <div class="slides--infos">
                                <div class="slide-info" data-current>
                                    <div class="slide-info__inner"><div class="slide-info--text__wrapper">
                                        <div data-title class="slide-info--text"><span>Arquitectura</span></div>
                                        <div data-subtitle class="slide-info--text"><span>Robusta</span></div>
                                        <div data-description class="slide-info--text"><span>Diseñada para crecer con tu empresa</span></div>
                                    </div></div>
                                </div>
                                <div class="slide-info" data-next>
                                    <div class="slide-info__inner"><div class="slide-info--text__wrapper">
                                        <div data-title class="slide-info--text"><span>Seguridad</span></div>
                                        <div data-subtitle class="slide-info--text"><span>Empresarial</span></div>
                                        <div data-description class="slide-info--text"><span>Roles, permisos y encriptación avanzada</span></div>
                                    </div></div>
                                </div>
                                <div class="slide-info" data-previous>
                                    <div class="slide-info__inner"><div class="slide-info--text__wrapper">
                                        <div data-title class="slide-info--text"><span>API &amp;</span></div>
                                        <div data-subtitle class="slide-info--text"><span>Integraciones</span></div>
                                        <div data-description class="slide-info--text"><span>Conecta con cualquier sistema existente</span></div>
                                    </div></div>
                                </div>
                            </div>
                        </div>
                        <button class="slider--btn slider--btn__next" aria-label="Siguiente">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════ TIPOS DE APLICACIONES ═══════════════ --}}
<section class="bg-surface-light dark:bg-surface-dark
                border-y border-gray-100 dark:border-white/[.06]"
         style="padding-top:5.5rem;">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-14">
            <div>
                <div class="flex items-center gap-3 mb-4" data-reveal>
                    <span class="block w-8 h-0.5 bg-brand dark:bg-primary-400 rounded"></span>
                    <span class="font-clash font-bold text-[.65rem] uppercase tracking-[.2em]
                                 text-brand dark:text-primary-400">Qué construimos</span>
                </div>
                <h2 class="font-clash font-black text-gray-900 dark:text-white
                           leading-[.97] tracking-tight"
                    style="font-size:clamp(2.2rem,5vw,3.5rem);"
                    data-reveal data-reveal-delay="1">
                    Tipos de<br>aplicaciones web
                </h2>
            </div>
            <p class="font-archivo text-gray-500 dark:text-gray-400 max-w-xs leading-relaxed lg:text-right"
               style="font-size:.9rem;font-weight:300;"
               data-reveal data-reveal-delay="2">
                Desde módulos específicos hasta sistemas empresariales completos, cada solución construida desde cero.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3
                    border border-gray-100 dark:border-white/[.06] rounded-sm overflow-hidden">
            @php
                $apps = [
                    ['icon'=>'widgets',      'title'=>'Sistemas ERP',            'desc'=>'Control total: ventas, compras, inventario, contabilidad, RRHH y más en un mismo sistema.'],
                    ['icon'=>'storefront',   'title'=>'E-commerce y tiendas',    'desc'=>'Catálogos, carritos, pasarelas de pago y panel administrativo integrado.'],
                    ['icon'=>'donut_large',  'title'=>'Dashboards y reportes',   'desc'=>'Visualización de datos en tiempo real, KPIs y reportes personalizados exportables.'],
                    ['icon'=>'group',        'title'=>'CRM',                     'desc'=>'Gestión de clientes, historial de interacciones, seguimiento de ventas y automatización.'],
                    ['icon'=>'task_alt',     'title'=>'Gestión de proyectos',    'desc'=>'Plataformas para administrar tareas, equipos, tiempos y entregables.'],
                    ['icon'=>'receipt_long', 'title'=>'Facturación electrónica', 'desc'=>'Módulos de facturación con timbrado, cancelación y seguimiento fiscal completo.'],
                ];
            @endphp
            @foreach($apps as $i => $app)
            <div class="app-card group p-7
                        bg-background-light dark:bg-background-dark
                        border-r border-b border-gray-100 dark:border-white/[.06]
                        {{ ($i+1) % 3 === 0 ? 'lg:border-r-0' : '' }}
                        {{ $i >= 3          ? 'border-b-0'    : '' }}"
                 data-reveal data-reveal-delay="{{ min($i % 3, 3) }}">
                <p class="font-clash font-bold text-[.62rem] tracking-[.15em]
                           text-gray-400 dark:text-gray-600 group-hover:text-white/50
                           transition-colors duration-200 mb-5">
                    0{{ $i + 1 }}
                </p>
                <div class="w-11 h-11 bg-primary-50 dark:bg-primary-950/60 group-hover:bg-white/20
                            flex items-center justify-center rounded-sm mb-4
                            transition-colors duration-200">
                    <span class="material-symbols-outlined text-brand dark:text-primary-400
                                 group-hover:text-white text-[20px] transition-colors duration-200"
                          style="font-variation-settings:'FILL' 0,'wght' 300">{{ $app['icon'] }}</span>
                </div>
                <h3 class="font-clash font-bold text-gray-900 dark:text-white group-hover:text-white
                           mb-2 transition-colors duration-200" style="font-size:1.05rem;">
                    {{ $app['title'] }}
                </h3>
                <p class="font-archivo text-gray-500 dark:text-gray-400 group-hover:text-white/75
                          leading-relaxed transition-colors duration-200"
                   style="font-size:.84rem;font-weight:300;">
                    {{ $app['desc'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ TECNOLOGÍAS ═══════════════ --}}
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-14 items-center">
            <div>
                <div class="flex items-center gap-3 mb-4" data-reveal>
                    <span class="block w-8 h-0.5 bg-brand dark:bg-primary-400 rounded"></span>
                    <span class="font-clash font-bold text-[.65rem] uppercase tracking-[.2em]
                                 text-brand dark:text-primary-400">Stack tecnológico</span>
                </div>
                <h2 class="font-clash font-black text-gray-900 dark:text-white
                           leading-[.97] tracking-tight mb-5"
                    style="font-size:clamp(2rem,4.5vw,3.4rem);"
                    data-reveal data-reveal-delay="1">
                    Tecnologías<br>que dominamos
                </h2>
                <p class="font-archivo text-gray-500 dark:text-gray-400 leading-relaxed"
                   style="font-size:.97rem;font-weight:300;max-width:46ch;"
                   data-reveal data-reveal-delay="2">
                    Seleccionamos la combinación ideal de herramientas para cada proyecto.
                    Priorizamos robustez, rendimiento y la capacidad de tu equipo para mantenerlas a largo plazo.
                </p>
            </div>
            <div class="flex flex-wrap gap-2.5" data-reveal data-reveal-delay="2">
                @foreach(['Phalcon','Laravel','Django','React','Vue.js','MySQL','PostgreSQL','MongoDB','Redis','Docker','Nginx','AWS'] as $tech)
                <span class="tech-pill font-clash font-bold text-[.76rem] tracking-[.08em] uppercase
                             px-5 py-2.5 rounded-sm
                             border border-gray-200 dark:border-white/[.1]
                             text-gray-600 dark:text-gray-400
                             hover:text-white hover:border-brand dark:hover:border-brand">
                    {{ $tech }}
                </span>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ CROSSLINK MÓVIL ═══════════════ --}}
<section class="py-20 bg-surface-light dark:bg-surface-dark
                border-y border-gray-100 dark:border-white/[.06]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-sm
                    bg-background-light dark:bg-background-dark
                    border border-gray-100 dark:border-white/[.08]
                    p-10 lg:p-14
                    hover:border-brand dark:hover:border-brand/50
                    transition-colors duration-300"
             data-reveal>
            <div class="absolute top-0 right-0 w-64 h-64 pointer-events-none"
                 style="background:radial-gradient(circle at top right, rgba(37,99,235,.1), transparent 70%);transform:translate(30%,-30%)">
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                <div class="max-w-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="block w-8 h-0.5 bg-brand dark:bg-primary-400 rounded"></span>
                        <span class="font-clash font-bold text-[.65rem] uppercase tracking-[.2em]
                                     text-brand dark:text-primary-400">Complementa tu solución</span>
                    </div>
                    <h2 class="font-clash font-black text-gray-900 dark:text-white
                               leading-tight tracking-tight mb-4"
                        style="font-size:clamp(1.7rem,3vw,2.6rem);">
                        ¿Necesitas también<br>una app móvil?
                    </h2>
                    <p class="font-archivo text-gray-500 dark:text-gray-400 leading-relaxed mb-7"
                       style="font-size:.95rem;font-weight:300;">
                        Tu sistema web puede extenderse al móvil. Desarrollamos apps nativas e híbridas que se sincronizan en tiempo real con tu plataforma, permitiendo capturar evidencias, consultar reportes o gestionar todo desde cualquier dispositivo.
                    </p>
                    <a href="{{ route('public.servicios.movil') }}"
                       class="inline-flex items-center gap-2
                              font-clash font-bold text-[.78rem] uppercase tracking-[.12em]
                              text-brand dark:text-primary-400
                              border-b border-brand/30 dark:border-primary-400/30 pb-0.5
                              hover:border-brand dark:hover:border-primary-400
                              hover:gap-3.5 transition-all duration-200">
                        <span class="material-symbols-outlined text-[1rem]">smartphone</span>
                        Conoce desarrollo móvil
                        <span class="material-symbols-outlined text-[.9rem]">arrow_forward</span>
                    </a>
                </div>
                <div class="font-clash font-black text-gray-100 dark:text-white/[.04]
                            hidden lg:block select-none"
                     style="font-size:clamp(5rem,10vw,9rem);line-height:.9;letter-spacing:-.04em;"
                     aria-hidden="true">APP</div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ CTA FINAL ═══════════════ --}}
<section class="relative overflow-hidden bg-brand py-24">
    <div class="absolute inset-0 opacity-[.07]"
         style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')">
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
            <div class="max-w-2xl">
                <h2 class="font-clash font-black text-white leading-[.95] tracking-tight mb-5"
                    style="font-size:clamp(2.4rem,6vw,5rem);"
                    data-reveal>
                    ¿Necesitas un<br>sistema a medida?
                </h2>
                <p class="font-archivo text-white/70 leading-relaxed mb-8"
                   style="font-size:1.05rem;font-weight:300;max-width:46ch;"
                   data-reveal data-reveal-delay="1">
                    Cuéntanos qué necesitas y te prepararemos una propuesta técnica y económica sin compromiso.
                </p>
                <a href="{{ route('public.contacto') }}"
                   class="inline-flex items-center gap-2
                          bg-white hover:bg-blue-50 active:bg-blue-100 text-brand
                          font-clash font-black text-[.84rem] uppercase tracking-[.1em]
                          px-8 py-4 rounded-sm
                          shadow-lg shadow-black/20 hover:shadow-xl
                          hover:-translate-y-0.5 hover:scale-[1.01]
                          transition-all duration-200"
                   data-reveal data-reveal-delay="2">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    Solicitar cotización
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
            <div class="font-clash font-black text-white/10 hidden lg:block select-none"
                 style="font-size:clamp(8rem,15vw,14rem);line-height:.9;letter-spacing:-.05em;"
                 aria-hidden="true">GO</div>
        </div>
    </div>
</section>

{{-- ═══════════════ SCRIPTS ═══════════════ --}}
<script>
(function () {
    /* Reveal */
    const els = document.querySelectorAll('[data-reveal]');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
            });
        }, { threshold: .12 });
        els.forEach(el => io.observe(el));
    } else {
        els.forEach(el => el.classList.add('is-visible'));
    }

    /* Slider 3D */
    const lerp  = (a, b, t) => a + (b - a) * t;
    const genId = (() => { let c = 0; return () => (c++).toString(); })();

    class Raf {
        constructor() { this.cbs = []; this.loop(); }
        loop() { this.cbs.forEach(({cb}) => cb()); requestAnimationFrame(this.loop.bind(this)); }
        add(cb)      { const id = genId(); this.cbs.push({cb, id}); return id; }
        remove(id)   { this.cbs = this.cbs.filter(c => c.id !== id); }
    }
    const raf = new Raf();

    class Vec2 {
        constructor(x=0,y=0){ this.x=x; this.y=y; }
        set(x,y){ this.x=x; this.y=y; }
        lerp(v,t){ this.x=lerp(this.x,v.x,t); this.y=lerp(this.y,v.y,t); }
    }

    function tilt(node, opts={}) {
        const trigger = opts.trigger ?? node;
        const targets = opts.target ? (Array.isArray(opts.target) ? opts.target : [opts.target]) : [node];
        const mobile  = window.matchMedia('(pointer:coarse)').matches;
        let amt = .06;
        const rot = { c: new Vec2(), t: new Vec2() };
        const bg  = { c: new Vec2(), t: new Vec2() };

        const id = raf.add(() => {
            rot.c.lerp(rot.t, amt); bg.c.lerp(bg.t, amt);
            targets.forEach(el => {
                el.style.setProperty('--rotX',   rot.c.y.toFixed(2)+'deg');
                el.style.setProperty('--rotY',   rot.c.x.toFixed(2)+'deg');
                el.style.setProperty('--bgPosX', bg.c.x.toFixed(2)+'%');
                el.style.setProperty('--bgPosY', bg.c.y.toFixed(2)+'%');
            });
        });

        if (!mobile) {
            trigger.addEventListener('mousemove', ({offsetX,offsetY}) => {
                amt = .1;
                targets.forEach(el => {
                    const ox = (offsetX - el.clientWidth  * .5) / (Math.PI * 3);
                    const oy = -(offsetY - el.clientHeight * .5) / (Math.PI * 4);
                    rot.t.set(ox, oy); bg.t.set(-ox*.3, oy*.3);
                });
            });
            trigger.addEventListener('mouseleave', () => {
                amt = .06; rot.t.set(0,0); bg.t.set(0,0);
            });
        }
        return { destroy: () => raf.remove(id) };
    }

    function change(dir) {
        return () => {
            const q = s => document.querySelector(s);

            let cur  = { s:q('.slide[data-current]'),  i:q('.slide-info[data-current]'),  bg:q('.slide__bg[data-current]')  };
            let prev = { s:q('.slide[data-previous]'), i:q('.slide-info[data-previous]'), bg:q('.slide__bg[data-previous]') };
            let next = { s:q('.slide[data-next]'),     i:q('.slide-info[data-next]'),     bg:q('.slide__bg[data-next]')     };

            [cur,prev,next].forEach(g => Object.values(g).forEach(el => {
                el?.removeAttribute('data-current');
                el?.removeAttribute('data-previous');
                el?.removeAttribute('data-next');
            }));

            if (dir === 1) {
                [cur.s.style.zIndex,prev.s.style.zIndex,next.s.style.zIndex] = ['20','30','10'];
                [cur,next,prev] = [next,prev,cur];
            } else {
                [cur.s.style.zIndex,prev.s.style.zIndex,next.s.style.zIndex] = ['20','10','30'];
                [cur,prev,next] = [prev,next,cur];
            }

            Object.values(cur).forEach(el  => el?.setAttribute('data-current',  ''));
            Object.values(prev).forEach(el => el?.setAttribute('data-previous', ''));
            Object.values(next).forEach(el => el?.setAttribute('data-next',     ''));
        };
    }

    function initSlider() {
        const slides     = [...document.querySelectorAll('.slide')];
        const slidesInfo = [...document.querySelectorAll('.slide-info')];
        const btnPrev    = document.querySelector('.slider--btn__prev');
        const btnNext    = document.querySelector('.slider--btn__next');
        const sliderEl   = document.querySelector('.slider');

        slides.forEach((slide, i) => {
            tilt(slide, { target: [
                slide.querySelector('.slide__inner'),
                slidesInfo[i].querySelector('.slide-info__inner'),
            ]});
        });

        const goPrev = change(-1);
        const goNext = change(1);
        btnPrev?.addEventListener('click', goPrev);
        btnNext?.addEventListener('click', goNext);

        let tx = 0;
        sliderEl?.addEventListener('touchstart', e => { tx = e.changedTouches[0].screenX; }, {passive:true});
        sliderEl?.addEventListener('touchend',   e => {
            const dx = e.changedTouches[0].screenX - tx;
            if (dx < -50) goNext();
            if (dx >  50) goPrev();
        }, {passive:true});
    }

    document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', initSlider)
        : initSlider();
})();
</script>

@endsection
