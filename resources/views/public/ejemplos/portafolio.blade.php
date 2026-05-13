@extends('layouts.public')
@section('title', 'Portafolio de Proyectos — JVJ Technology')
@section('meta_description', 'Proyectos desarrollados por JVJ Technology: ERP, apps móviles, e-commerce y sistemas web
    para empresas en México y Latinoamérica.')

@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Archivo:wght@300;400;500;600;700&display=swap');

        :root {
            --ease-out: cubic-bezier(.16, 1, .3, 1);
            --ease-spring: cubic-bezier(.34, 1.56, .64, 1);
        }

        html { overflow-x: hidden; }
        .font-clash  { font-family: "Clash Display", sans-serif; }
        .font-archivo{ font-family: "Archivo", sans-serif; }

        /* ── Reveal ── */
        [data-reveal] {
            opacity: 0; transform: translateY(20px);
            transition: opacity .6s var(--ease-out), transform .6s var(--ease-out);
        }
        [data-reveal].is-visible  { opacity: 1; transform: none; }
        [data-reveal-delay="1"]   { transition-delay: .1s; }
        [data-reveal-delay="2"]   { transition-delay: .2s; }
        [data-reveal-delay="3"]   { transition-delay: .3s; }

        /* ── SHOWCASE SLIDER ── */
        .showcase { position: relative; width: 100%; overflow: hidden; }
        .showcase-track { display: flex; transition: transform .75s var(--ease-out); will-change: transform; }
        .showcase-slide { min-width: 100%; display: grid; grid-template-columns: 1fr 1fr; min-height: 580px; }
        @media (max-width: 900px) { .showcase-slide { grid-template-columns: 1fr; min-height: auto; } }

        /* ── Image panel ── */
        .showcase-img-wrap {
            position: relative; overflow: hidden;
            background: #0f172a; cursor: zoom-in;
        }
        .showcase-img-wrap img {
            width: 100%; height: 100%; object-fit: cover; display: block;
            /* Scale so pan has room to move without white edges */
            transform: scale(1.1) translate(0%, 0%);
            transform-origin: center center;
            transition: transform .5s var(--ease-out), filter .4s;
            filter: brightness(.75) saturate(.9);
            will-change: transform;
            pointer-events: none;
        }
        .showcase-slide.is-active .showcase-img-wrap img { filter: brightness(.88) saturate(1); }

        .showcase-img-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to right, transparent 60%, rgba(0,0,0,.1));
            pointer-events: none;
            transition: opacity .3s;
        }
        .showcase-img-wrap:hover .showcase-img-overlay { opacity: .4; }

        /* Hover overlay layer */
        .img-hover-layer {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,0);
            transition: background .25s;
            pointer-events: none;
            z-index: 2;
        }
        .showcase-img-wrap:hover .img-hover-layer { background: rgba(0,0,0,.22); }

        /* Main expand button */
        .img-expand-btn {
            display: inline-flex; align-items: center; gap: .55rem;
            background: white; color: #1e3a8a;
            font-family: "Clash Display", sans-serif;
            font-weight: 700; font-size: .72rem;
            letter-spacing: .12em; text-transform: uppercase;
            padding: .65rem 1.3rem; border-radius: 2px;
            opacity: 0; transform: translateY(10px) scale(.95);
            transition: opacity .22s var(--ease-out), transform .22s var(--ease-out);
            pointer-events: none;
            box-shadow: 0 8px 24px rgba(0,0,0,.35);
            white-space: nowrap;
        }
        .showcase-img-wrap:hover .img-expand-btn {
            opacity: 1; transform: translateY(0) scale(1);
        }

        /* Corner expand icon */
        .img-corner-icon {
            position: absolute; bottom: .9rem; right: .9rem;
            width: 2.2rem; height: 2.2rem; z-index: 3;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.28);
            border-radius: 2px; backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transform: scale(.8);
            transition: opacity .22s, transform .22s var(--ease-spring);
            pointer-events: none;
        }
        .showcase-img-wrap:hover .img-corner-icon { opacity: 1; transform: scale(1); }

        /* Cat badge */
        .img-cat-badge {
            position: absolute; top: 1rem; left: 1rem; z-index: 4;
            font-family: "Clash Display", sans-serif; font-weight: 700;
            font-size: .62rem; text-transform: uppercase; letter-spacing: .15em;
            color: white; padding: .4rem .9rem; border-radius: 2px;
            border: 1px solid rgba(255,255,255,.22);
            background: rgba(0,0,0,.38); backdrop-filter: blur(6px);
            pointer-events: none;
        }

        /* ── Info panel ── */
        .showcase-info {
            display: flex; flex-direction: column; justify-content: center;
            padding: 3.5rem 3rem 3.5rem 4rem; position: relative; overflow: hidden;
        }
        @media (max-width: 1100px) { .showcase-info { padding: 2.5rem 2rem; } }
        @media (max-width: 900px)  { .showcase-info { padding: 2rem 1.5rem; } }

        /* Stagger animation */
        .showcase-content > * {
            opacity: 0; transform: translateY(18px);
            transition: opacity .55s var(--ease-out), transform .55s var(--ease-out);
        }
        .showcase-slide.is-active .showcase-content > * { opacity: 1; transform: none; }
        .showcase-slide.is-active .showcase-content > *:nth-child(1) { transition-delay: .08s; }
        .showcase-slide.is-active .showcase-content > *:nth-child(2) { transition-delay: .16s; }
        .showcase-slide.is-active .showcase-content > *:nth-child(3) { transition-delay: .22s; }
        .showcase-slide.is-active .showcase-content > *:nth-child(4) { transition-delay: .28s; }
        .showcase-slide.is-active .showcase-content > *:nth-child(5) { transition-delay: .34s; }
        .showcase-slide.is-active .showcase-content > *:nth-child(6) { transition-delay: .40s; }
        .showcase-slide.is-active .showcase-content > *:nth-child(7) { transition-delay: .46s; }

        .slide-num {
            font-family: "Clash Display", sans-serif;
            font-weight: 800; font-size: 7rem; line-height: 1;
            letter-spacing: -.05em; position: absolute;
            bottom: -1.5rem; right: 1.5rem;
            color: transparent; user-select: none; pointer-events: none;
        }

        /* Dots */
        .showcase-dots { display: flex; align-items: center; gap: .5rem; }
        .dot { width: 6px; height: 6px; border-radius: 50%; cursor: pointer; transition: all .25s var(--ease-spring); }
        .dot.active { width: 24px; border-radius: 3px; }

        /* Arrows */
        .showcase-arrow {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.6rem; height: 2.6rem; border-radius: 3px; border: 1px solid;
            cursor: pointer; transition: all .2s var(--ease-spring);
        }
        .showcase-arrow:hover { transform: scale(1.08); }

        /* Progress */
        .showcase-progress { position: absolute; bottom: 0; left: 0; height: 2px; background: #2563eb; transition: width .75s var(--ease-out); }

        /* Filter tabs */
        .filter-tab {
            padding: .45rem 1.1rem; border-radius: 2px;
            font-family: "Clash Display", sans-serif;
            font-weight: 700; font-size: .72rem;
            letter-spacing: .1em; text-transform: uppercase;
            cursor: pointer; border: 1px solid;
            transition: all .2s var(--ease-spring);
        }
        .filter-tab:hover { transform: translateY(-1px); }

        /* Thumbs */
        .thumb-strip { display: flex; gap: .75rem; overflow-x: auto; scrollbar-width: none; padding-bottom: .25rem; }
        .thumb-strip::-webkit-scrollbar { display: none; }
        .thumb {
            flex-shrink: 0; width: 90px; height: 60px;
            border-radius: 3px; overflow: hidden; cursor: pointer;
            border: 2px solid transparent;
            transition: border-color .2s, opacity .2s, transform .2s var(--ease-spring);
            opacity: .45;
        }
        .thumb:hover { opacity: .75; transform: translateY(-2px); }
        .thumb.active { opacity: 1; transform: translateY(-3px); }
        .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }

        /* ── LIGHTBOX ── */
        #lightbox {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,0);
            backdrop-filter: blur(0px) saturate(1);
            -webkit-backdrop-filter: blur(0px);
            transition: background .3s, backdrop-filter .3s;
            pointer-events: none;
        }
        #lightbox.lb-open {
            background: rgba(0,0,0,.93);
            backdrop-filter: blur(14px) saturate(1.2);
            -webkit-backdrop-filter: blur(14px) saturate(1.2);
            pointer-events: all;
        }
        .lb-box {
            position: relative; width: 100%;
            max-width: min(92vw, 1060px);
            opacity: 0; transform: scale(.94) translateY(14px);
            transition: opacity .3s var(--ease-out), transform .3s var(--ease-out);
            padding: 0 1rem;
        }
        #lightbox.lb-open .lb-box { opacity: 1; transform: scale(1) translateY(0); }

        #lightbox-img {
            display: block; width: 100%;
            max-height: 72vh; object-fit: contain;
            border-radius: 3px;
            box-shadow: 0 40px 80px rgba(0,0,0,.7);
            opacity: 1; transform: scale(1);
            transition: opacity .18s, transform .18s;
        }
        #lightbox-img.lb-switching { opacity: 0; transform: scale(.97); }

        /* Close */
        .lb-close {
            position: fixed; top: 1.1rem; right: 1.1rem;
            width: 2.8rem; height: 2.8rem;
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
            border-radius: 3px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: white;
            transition: background .2s, transform .2s var(--ease-spring), opacity .2s;
            opacity: 0; pointer-events: none;
        }
        #lightbox.lb-open .lb-close { opacity: 1; pointer-events: all; }
        .lb-close:hover { background: rgba(255,255,255,.2); transform: rotate(90deg) scale(1.08); }

        /* Nav arrows */
        .lb-arrow {
            position: fixed; top: 50%; transform: translateY(-50%);
            width: 3rem; height: 3rem;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);
            border-radius: 3px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: white;
            transition: background .2s, border-color .2s, opacity .2s;
            /* Hidden by default — shown only when lightbox is open */
            opacity: 0; pointer-events: none;
        }
        #lightbox.lb-open .lb-arrow { opacity: 1; pointer-events: all; }
        .lb-arrow:hover { background: #2563eb; border-color: #2563eb; }
        .lb-arrow-prev { left: 1.1rem; }
        .lb-arrow-next { right: 1.1rem; }

        /* Footer */
        .lb-footer {
            margin-top: .9rem; display: flex;
            align-items: flex-start; justify-content: space-between; gap: 1rem;
        }
        .lb-title { font-family: "Clash Display", sans-serif; font-weight: 700; font-size: 1rem; color: white; margin-bottom: .2rem; }
        .lb-meta  { font-family: "Archivo", sans-serif; font-size: .75rem; font-weight: 300; color: rgba(255,255,255,.4); }
        .lb-counter { font-family: "Clash Display", sans-serif; font-weight: 700; font-size: .75rem; color: rgba(255,255,255,.35); letter-spacing: .1em; white-space: nowrap; padding-top: .15rem; }

        /* LB thumbs */
        .lb-thumbs { display: flex; gap: .5rem; justify-content: center; margin-top: .9rem; overflow-x: auto; scrollbar-width: none; }
        .lb-thumbs::-webkit-scrollbar { display: none; }
        .lb-thumb {
            flex-shrink: 0; width: 56px; height: 38px;
            border-radius: 2px; overflow: hidden;
            cursor: pointer; opacity: .3; border: 2px solid transparent;
            transition: opacity .2s, border-color .2s, transform .2s var(--ease-spring);
        }
        .lb-thumb:hover { opacity: .6; transform: translateY(-2px); }
        .lb-thumb.active { opacity: 1; border-color: #2563eb; transform: translateY(-2px); }
        .lb-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    </style>

    {{-- ═══════ HERO ═══════ --}}
    <section class="relative overflow-hidden bg-primary-950 dark:bg-gray-950 text-white py-24">
        <div class="absolute inset-0 opacity-[.04]"
            style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')">
        </div>
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand rounded-full blur-3xl opacity-[.18]"></div>
        <div class="absolute -bottom-32 -left-24 w-96 h-96 bg-primary-700 rounded-full blur-3xl opacity-[.12]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div data-reveal>
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-sm
                                bg-white/10 border border-white/20
                                text-primary-300 text-xs font-clash font-bold uppercase tracking-widest mb-6">
                        <span class="material-symbols-outlined text-[14px]">work</span>
                        Nuestro trabajo
                    </div>
                    <h1 class="font-clash font-black leading-[.96] tracking-tight text-white"
                        style="font-size:clamp(3rem,8vw,6.5rem);">
                        Portafolio<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-400">
                            de Proyectos
                        </span>
                    </h1>
                </div>
                <div class="lg:text-right max-w-xs" data-reveal data-reveal-delay="2">
                    <p class="font-archivo text-primary-200/60 leading-relaxed" style="font-weight:300;font-size:.97rem;">
                        Proyectos reales para empresas reales. Cada solución construida desde cero.
                    </p>
                    <p class="font-clash font-black text-white/20 mt-2" style="font-size:3.5rem;line-height:1;letter-spacing:-.04em;">+80</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════ FILTROS ═══════ --}}
    <div class="sticky top-14 z-20 bg-surface-light/95 dark:bg-surface-dark/95 backdrop-blur-md
                border-b border-gray-200 dark:border-white/[.07] py-3.5"
         x-data="{ active: 'todos' }" id="filter-bar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-2">
                @foreach([
                    ['key'=>'todos',     'label'=>'Todos'],
                    ['key'=>'erp',       'label'=>'ERP'],
                    ['key'=>'movil',     'label'=>'Apps Móviles'],
                    ['key'=>'ecommerce', 'label'=>'E-commerce'],
                    ['key'=>'web',       'label'=>'Sistemas Web'],
                ] as $f)
                <button
                    @click="active = '{{ $f['key'] }}'; $dispatch('filter-change', '{{ $f['key'] }}')"
                    :class="active === '{{ $f['key'] }}'
                        ? 'bg-brand text-white border-brand'
                        : 'bg-background-light dark:bg-background-dark text-gray-600 dark:text-gray-400 border-gray-200 dark:border-white/[.10] hover:border-brand hover:text-brand dark:hover:text-primary-400'"
                    class="filter-tab">
                    {{ $f['label'] }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════ SHOWCASE SLIDER ═══════ --}}
    <section class="bg-background-light dark:bg-background-dark py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="showcase rounded-sm overflow-hidden border border-gray-100 dark:border-white/[.07]
                        shadow-2xl shadow-black/10 dark:shadow-black/40" id="showcase">

                <div class="showcase-track" id="showcase-track">
                    @php
                    $projects = [
                        ['img'=>'/img/fleetnet.png', 'cat'=>'ERP','cat_key'=>'erp','year'=>'2023','city'=>'CDMX, México','title'=>'Sistema ERP para Logística','desc'=>'ERP completo con control de flota, gestión de rutas y seguimiento de entregas en tiempo real. Integrado con GPS y facturación electrónica.','tags'=>['Laravel','Vue.js','MySQL','GPS API']],
                        ['img'=>'https://placehold.co/900x580/064e3b/ffffff?text=App+Ventas','cat'=>'App Móvil','cat_key'=>'movil','year'=>'2024','city'=>'Monterrey, México','title'=>'App de Ventas en Campo','desc'=>'Aplicación móvil para vendedores en ruta: toma de pedidos, catálogo sincronizado, cobranza y firma digital del cliente.','tags'=>['Flutter','Phalcon','PostgreSQL','Offline mode']],
                        ['img'=>'https://placehold.co/900x580/78350f/ffffff?text=ERP+Retail','cat'=>'ERP','cat_key'=>'erp','year'=>'2023','city'=>'Guadalajara, México','title'=>'ERP para Cadena de Tiendas','desc'=>'Sistema centralizado para 15 tiendas: inventario en tiempo real, ventas unificadas, facturación electrónica y reportes ejecutivos.','tags'=>['Laravel','React','MySQL','SAT CFDI']],
                        ['img'=>'https://placehold.co/900x580/3b0764/ffffff?text=App+Inventario','cat'=>'App Móvil','cat_key'=>'movil','year'=>'2024','city'=>'Puebla, México','title'=>'App de Inventario con Código de Barras','desc'=>'Gestión de inventario mediante lectura de códigos de barras y QR con modo offline completo. Sincronización automática al reconectar.','tags'=>['React Native','Django','SQLite','QR / Barcode']],
                        ['img'=>'https://placehold.co/900x580/7f1d1d/ffffff?text=E-commerce','cat'=>'E-commerce','cat_key'=>'ecommerce','year'=>'2023','city'=>'Querétaro, México','title'=>'Tienda Online + ERP Integrado','desc'=>'Tienda en línea con sincronización automática de inventario y pedidos hacia el ERP interno. Pasarela de pagos Stripe + SPEI.','tags'=>['Laravel','Vue.js','Stripe','MySQL']],
                        ['img'=>'https://placehold.co/900x580/172554/ffffff?text=ERP+Manufactura','cat'=>'ERP','cat_key'=>'erp','year'=>'2022','city'=>'Tijuana, México','title'=>'ERP para Planta de Manufactura','desc'=>'Planificación de producción, control de materia prima y órdenes de trabajo. Dashboard en tiempo real con KPIs de línea de producción.','tags'=>['Phalcon','React','PostgreSQL','WebSockets']],
                    ];
                    @endphp

                    @foreach($projects as $idx => $p)
                    <div class="showcase-slide {{ $idx === 0 ? 'is-active' : '' }}"
                         data-index="{{ $idx }}" data-cat="{{ $p['cat_key'] }}">

                        {{-- Imagen --}}
                        <div class="showcase-img-wrap"
                             data-idx="{{ $idx }}"
                             onclick="openLightbox({{ $idx }})">

                            <img src="{{ $p['img'] }}"
                                 alt="{{ $p['title'] }}"
                                 id="slide-img-{{ $idx }}"
                                 loading="{{ $idx > 1 ? 'lazy' : 'eager' }}">

                            <div class="showcase-img-overlay"></div>
                            <span class="img-cat-badge">{{ $p['cat'] }}</span>

                            {{-- Hover overlay --}}
                            <div class="img-hover-layer">
                                <div class="img-expand-btn">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
                                        <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
                                    </svg>
                                    Ver imagen completa
                                </div>
                            </div>

                            {{-- Corner icon --}}
                            <div class="img-corner-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
                                    <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="showcase-info bg-surface-light dark:bg-surface-dark">
                            <span class="slide-num text-gray-100 dark:text-white/[.04]">
                                {{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <div class="showcase-content">
                                {{-- Meta --}}
                                <div class="flex items-center gap-4 mb-4">
                                    <span class="font-archivo text-gray-400 dark:text-gray-500 text-xs flex items-center gap-1.5" style="font-weight:300;">
                                        <span class="material-symbols-outlined text-[13px]">calendar_today</span>{{ $p['year'] }}
                                    </span>
                                    <span class="font-archivo text-gray-400 dark:text-gray-500 text-xs flex items-center gap-1.5" style="font-weight:300;">
                                        <span class="material-symbols-outlined text-[13px]">location_on</span>{{ $p['city'] }}
                                    </span>
                                </div>

                                {{-- Título --}}
                                <h2 class="font-clash font-black text-gray-900 dark:text-white leading-tight tracking-tight mb-4"
                                    style="font-size:clamp(1.5rem,3vw,2.2rem);">
                                    {{ $p['title'] }}
                                </h2>

                                {{-- Desc --}}
                                <p class="font-archivo text-gray-500 dark:text-gray-400 leading-relaxed mb-5"
                                   style="font-size:.95rem;font-weight:300;max-width:46ch;">
                                    {{ $p['desc'] }}
                                </p>

                                {{-- Tags --}}
                                <div class="flex flex-wrap gap-2 mb-5">
                                    @foreach($p['tags'] as $tag)
                                    <span class="font-clash font-bold text-[.66rem] uppercase tracking-[.1em]
                                                 px-3 py-1 rounded-sm
                                                 bg-gray-100 dark:bg-white/[.06]
                                                 text-gray-600 dark:text-gray-400
                                                 border border-gray-200 dark:border-white/[.08]">
                                        {{ $tag }}
                                    </span>
                                    @endforeach
                                </div>

                                {{-- Expand link --}}
                                <button onclick="openLightbox({{ $idx }})"
                                        class="inline-flex items-center gap-2 mb-6
                                               font-clash font-bold text-[.7rem] uppercase tracking-[.1em]
                                               text-brand dark:text-primary-400
                                               hover:gap-3 transition-all duration-200">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
                                        <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
                                    </svg>
                                    Ver imagen completa
                                </button>

                                <div class="w-full h-px bg-gray-100 dark:bg-white/[.06] mb-6"></div>

                                {{-- Nav --}}
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                    <div class="showcase-dots"></div>
                                    <div class="flex items-center gap-2">
                                        <button class="showcase-arrow border-gray-200 dark:border-white/[.1]
                                                       text-gray-500 dark:text-gray-400
                                                       hover:bg-brand hover:border-brand hover:text-white
                                                       dark:hover:bg-brand dark:hover:border-brand dark:hover:text-white"
                                                onclick="showcasePrev()" aria-label="Anterior">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                        </button>
                                        <button class="showcase-arrow border-gray-200 dark:border-white/[.1]
                                                       text-gray-500 dark:text-gray-400
                                                       hover:bg-brand hover:border-brand hover:text-white
                                                       dark:hover:bg-brand dark:hover:border-brand dark:hover:text-white"
                                                onclick="showcaseNext()" aria-label="Siguiente">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>{{-- /track --}}
                <div class="showcase-progress" id="showcase-progress" style="width:16.666%"></div>
            </div>{{-- /showcase --}}

            {{-- Thumbs --}}
            <div class="mt-5" data-reveal>
                <div class="thumb-strip" id="thumb-strip">
                    @foreach($projects as $idx => $p)
                    <div class="thumb {{ $idx === 0 ? 'active' : '' }}"
                         onclick="showcaseGoto({{ $idx }})"
                         style="{{ $idx === 0 ? 'border-color:#2563eb' : '' }}"
                         id="thumb-{{ $idx }}">
                        <img src="{{ $p['img'] }}" alt="{{ $p['title'] }}">
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between mt-4">
                <p class="font-clash font-bold text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest">
                    <span id="current-num">01</span>
                    <span class="mx-1.5 opacity-40">/</span>
                    <span>{{ str_pad(count($projects), 2, '0', STR_PAD_LEFT) }}</span>
                </p>
                <p class="font-archivo text-xs text-gray-400 dark:text-gray-600 flex items-center gap-1.5" style="font-weight:300;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                        <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
                        <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
                    </svg>
                    Pasa el cursor para explorar · Clic para ver completa
                </p>
            </div>
        </div>
    </section>

    {{-- ═══════ CTA ═══════ --}}
    <section class="relative overflow-hidden bg-brand py-24">
        <div class="absolute inset-0 opacity-[.07]"
            style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23fff fill-rule=evenodd%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/svg%3E')">
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                <div class="max-w-2xl">
                    <h2 class="font-clash font-black text-white leading-[.95] tracking-tight mb-5"
                        style="font-size:clamp(2.4rem,6vw,5rem);" data-reveal>
                        ¿Quieres un<br>proyecto similar?
                    </h2>
                    <p class="font-archivo text-white/70 leading-relaxed mb-8"
                       style="font-size:1.05rem;font-weight:300;max-width:46ch;"
                       data-reveal data-reveal-delay="1">
                        Hablemos y construyamos la solución perfecta para tu negocio.
                    </p>
                    <a href="{{ route('public.contacto') }}"
                       class="inline-flex items-center gap-2
                              bg-white hover:bg-blue-50 active:bg-blue-100 text-brand
                              font-clash font-black text-[.84rem] uppercase tracking-[.1em]
                              px-8 py-4 rounded-sm shadow-lg shadow-black/20 hover:shadow-xl
                              hover:-translate-y-0.5 hover:scale-[1.01] transition-all duration-200"
                       data-reveal data-reveal-delay="2">
                        <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                        Iniciar proyecto
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
                <div class="font-clash font-black text-white/10 hidden lg:block select-none"
                     style="font-size:clamp(8rem,15vw,14rem);line-height:.9;letter-spacing:-.05em;" aria-hidden="true">YO</div>
            </div>
        </div>
    </section>

    {{-- ═══════ LIGHTBOX ═══════ --}}
    <div id="lightbox" role="dialog" aria-modal="true" aria-label="Vista de imagen">
        <div class="lb-box" id="lb-box">
            <img id="lightbox-img" src="" alt="Imagen ampliada">
            <div class="lb-footer">
                <div>
                    <div class="lb-title" id="lb-title"></div>
                    <div class="lb-meta"  id="lb-meta"></div>
                </div>
                <div class="lb-counter" id="lb-counter"></div>
            </div>
            <div class="lb-thumbs" id="lb-thumbs"></div>
        </div>

        <button class="lb-close" onclick="closeLightbox()" aria-label="Cerrar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <button class="lb-arrow lb-arrow-prev" onclick="lbPrev()" aria-label="Anterior">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <button class="lb-arrow lb-arrow-next" onclick="lbNext()" aria-label="Siguiente">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </div>

    <script>
    (function () {
        /* ── Reveal ── */
        const revEls = document.querySelectorAll('[data-reveal]');
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver(entries => {
                entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); } });
            }, { threshold: .1 });
            revEls.forEach(el => io.observe(el));
        } else { revEls.forEach(el => el.classList.add('is-visible')); }

        /* ────────────────────────────────
           IMAGE PAN ON HOVER
        ──────────────────────────────── */
        const SCALE  = 1.1;   // matches CSS
        const TRAVEL = 4.5;   // % of image size

        document.querySelectorAll('.showcase-img-wrap').forEach(wrap => {
            const img = wrap.querySelector('img');

            wrap.addEventListener('mousemove', e => {
                const r  = wrap.getBoundingClientRect();
                const nx = (e.clientX - r.left) / r.width  * 2 - 1;  // -1..+1
                const ny = (e.clientY - r.top)  / r.height * 2 - 1;
                const tx = -nx * TRAVEL;
                const ty = -ny * TRAVEL;
                img.style.transition = 'transform .07s linear, filter .4s';
                img.style.transform  = `scale(${SCALE}) translate(${tx}%, ${ty}%)`;
            });

            wrap.addEventListener('mouseleave', () => {
                img.style.transition = 'transform .55s cubic-bezier(.16,1,.3,1), filter .4s';
                img.style.transform  = `scale(${SCALE}) translate(0%, 0%)`;
            });
        });

        /* ────────────────────────────────
           SHOWCASE SLIDER
        ──────────────────────────────── */
        const track      = document.getElementById('showcase-track');
        const slides     = [...document.querySelectorAll('.showcase-slide')];
        const progressEl = document.getElementById('showcase-progress');
        const currentEl  = document.getElementById('current-num');
        const total      = slides.length;
        let current      = 0;
        let autoTimer    = null;

        function buildDots() {
            document.querySelectorAll('.showcase-dots').forEach(c => {
                c.innerHTML = '';
                slides.forEach((_, i) => {
                    const d = document.createElement('button');
                    d.className = 'dot ' + (i === current ? 'active bg-brand' : 'bg-gray-300 dark:bg-white/20');
                    d.setAttribute('aria-label', 'Ir a proyecto ' + (i + 1));
                    d.addEventListener('click', () => showcaseGoto(i));
                    c.appendChild(d);
                });
            });
        }

        function updateDots() {
            document.querySelectorAll('.showcase-dots').forEach(c => {
                c.querySelectorAll('.dot').forEach((d, i) => {
                    d.className = 'dot ' + (i === current ? 'active bg-brand' : 'bg-gray-300 dark:bg-white/20');
                });
            });
        }

        function updateThumbs() {
            slides.forEach((_, i) => {
                const t = document.getElementById('thumb-' + i);
                if (!t) return;
                t.classList.toggle('active', i === current);
                t.style.borderColor = i === current ? '#2563eb' : 'transparent';
            });
            const at = document.getElementById('thumb-' + current);
            if (at) at.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }

        function goTo(idx) {
            slides[current].classList.remove('is-active');
            current = (idx + total) % total;
            slides[current].classList.add('is-active');
            track.style.transform  = `translateX(-${current * 100}%)`;
            progressEl.style.width = `${((current + 1) / total) * 100}%`;
            currentEl.textContent  = String(current + 1).padStart(2, '0');
            updateDots();
            updateThumbs();
        }

        window.showcaseNext = () => { goTo(current + 1); resetAuto(); };
        window.showcasePrev = () => { goTo(current - 1); resetAuto(); };
        window.showcaseGoto = (i)  => { goTo(i);         resetAuto(); };

        function startAuto() { autoTimer = setInterval(() => goTo(current + 1), 5500); }
        function resetAuto() { clearInterval(autoTimer); startAuto(); }

        /* Swipe */
        let tx = 0;
        const sc = document.getElementById('showcase');
        sc.addEventListener('touchstart', e => { tx = e.changedTouches[0].screenX; }, { passive: true });
        sc.addEventListener('touchend',   e => {
            const dx = e.changedTouches[0].screenX - tx;
            if (dx < -50) window.showcaseNext();
            if (dx >  50) window.showcasePrev();
        }, { passive: true });

        /* Keyboard */
        document.addEventListener('keydown', e => {
            if (lb.classList.contains('lb-open')) return; // let lightbox handle it
            if (e.key === 'ArrowRight') window.showcaseNext();
            if (e.key === 'ArrowLeft')  window.showcasePrev();
        });

        buildDots();
        slides[0].classList.add('is-active');
        startAuto();

        document.addEventListener('filter-change', e => {
            const cat   = e.detail;
            const found = slides.find(s => cat === 'todos' || s.dataset.cat === cat);
            if (found) showcaseGoto(parseInt(found.dataset.index));
        });

        /* ────────────────────────────────
           LIGHTBOX
        ──────────────────────────────── */
        const projects = {!! json_encode(array_map(function($p) {
            return ['img' => $p['img'], 'title' => $p['title'], 'year' => $p['year'], 'city' => $p['city']];
        }, $projects)) !!};

        const lb        = document.getElementById('lightbox');
        const lbImg     = document.getElementById('lightbox-img');
        const lbTitle   = document.getElementById('lb-title');
        const lbMeta    = document.getElementById('lb-meta');
        const lbCounter = document.getElementById('lb-counter');
        const lbThumbs  = document.getElementById('lb-thumbs');
        let lbIdx       = 0;

        // Build lb thumbs once
        projects.forEach((p, i) => {
            const t = document.createElement('div');
            t.className = 'lb-thumb';
            t.innerHTML = `<img src="${p.img}" alt="${p.title}">`;
            t.addEventListener('click', () => lbGoTo(i));
            lbThumbs.appendChild(t);
        });

        function lbGoTo(idx) {
            lbIdx = (idx + projects.length) % projects.length;
            const p = projects[lbIdx];

            // Animate swap
            lbImg.classList.add('lb-switching');
            setTimeout(() => {
                lbImg.src = p.img;
                lbImg.onload = () => lbImg.classList.remove('lb-switching');
                // If already cached, onload may not fire
                if (lbImg.complete) lbImg.classList.remove('lb-switching');
                lbTitle.textContent   = p.title;
                lbMeta.textContent    = p.year + ' · ' + p.city;
                lbCounter.textContent = String(lbIdx + 1).padStart(2,'0') + ' / ' + String(projects.length).padStart(2,'0');
            }, 140);

            lbThumbs.querySelectorAll('.lb-thumb').forEach((t, i) => {
                t.classList.toggle('active', i === lbIdx);
                if (i === lbIdx) t.scrollIntoView({ behavior:'smooth', block:'nearest', inline:'center' });
            });
        }

        window.openLightbox = function(idx) {
            lbGoTo(idx);
            lb.classList.add('lb-open');
            document.body.style.overflow = 'hidden';
        };
        window.closeLightbox = function() {
            lb.classList.remove('lb-open');
            document.body.style.overflow = '';
        };
        window.lbNext = () => lbGoTo(lbIdx + 1);
        window.lbPrev = () => lbGoTo(lbIdx - 1);

        lb.addEventListener('click', e => { if (e.target === lb) closeLightbox(); });

        document.addEventListener('keydown', e => {
            if (!lb.classList.contains('lb-open')) return;
            if (e.key === 'Escape')     closeLightbox();
            if (e.key === 'ArrowRight') lbNext();
            if (e.key === 'ArrowLeft')  lbPrev();
        });

        // Touch swipe in lightbox
        let lbTx = 0;
        lb.addEventListener('touchstart', e => { lbTx = e.changedTouches[0].screenX; }, { passive: true });
        lb.addEventListener('touchend',   e => {
            const dx = e.changedTouches[0].screenX - lbTx;
            if (dx < -50) lbNext();
            if (dx >  50) lbPrev();
        }, { passive: true });
    })();
    </script>

@endsection
