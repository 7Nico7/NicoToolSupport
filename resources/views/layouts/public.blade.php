<!DOCTYPE html>
<html lang="es" x-data="layoutApp()" :class="{ 'dark': darkMode }" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'JVJ Technology — Soluciones tecnológicas a medida para empresas que quieren crecer.')">
    <title>@yield('title', 'JVJ Technology') — Soluciones Tecnológicas</title>

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..900;1,14..32,300..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    {{-- Font Awesome solo para íconos de redes sociales --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- AOS --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')

    <style>
        /* Material Symbols rendering */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            line-height: 1;
            vertical-align: middle;
            user-select: none;
        }
        .ms-filled { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

        /* Scroll behavior for sticky nav shadow */
        [x-ref="navbar"] { transition: box-shadow 0.2s ease, background 0.2s ease; }

        /* Dropdown triangle caret */
        .nav-dropdown-panel::before {
            content: '';
            position: absolute;
            top: -5px;
            left: 24px;
            width: 10px;
            height: 10px;
            background: inherit;
            border-left: 1px solid;
            border-top: 1px solid;
            border-color: inherit;
            transform: rotate(45deg);
        }

        /* Smooth dropdown */
        [x-cloak] { display: none !important; }

        /* Focus ring override for brand color */
        .focus-brand:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgb(37 99 235 / 0.25);
        }
    </style>
</head>

<body class="font-sans bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 antialiased transition-colors duration-200">

{{-- ══════════════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════════ --}}
<header
    x-ref="navbar"
    :class="scrolled ? 'shadow-lg shadow-black/5 dark:shadow-black/20' : ''"
    class="fixed top-0 inset-x-0 z-50 bg-surface-light/95 dark:bg-surface-dark/95 backdrop-blur-md border-b border-gray-200/60 dark:border-white/[0.06] transition-all duration-200">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- ── Logo ─────────────────────────────────────────────────── --}}
            <a href="{{ route('public.home') }}"
               class="flex items-center gap-2.5 shrink-0 group focus-brand rounded-lg">
                {{-- Brand mark --}}
                <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center shadow-sm shadow-brand/30 group-hover:bg-primary-700 transition-colors duration-200">
                    <span class="material-symbols-outlined text-white text-[18px] ms-filled">bolt</span>
                </div>
                {{-- Wordmark --}}
                <div class="leading-tight">
                    <span class="block text-sm font-black text-gray-900 dark:text-white tracking-tight">JVJ Technology</span>
                    <span class="block text-[10px] font-medium text-primary-600 dark:text-primary-400 uppercase tracking-widest leading-none">Soluciones TI</span>
                </div>
            </a>

            {{-- ── Desktop Nav ──────────────────────────────────────────── --}}
            <nav class="hidden lg:flex items-center gap-1" aria-label="Navegación principal">

                {{-- Inicio --}}
                <a href="{{ route('public.home') }}"
                   class="nav-link px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('public.home') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/60 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06]' }}">
                    Inicio
                </a>

                {{-- Quiénes Somos dropdown --}}
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                    <button @click="open = !open"
                            :aria-expanded="open"
                            class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 focus-brand
                                   {{ request()->routeIs('public.nosotros*','public.equipo','public.mision-vision','public.valores') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/60 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06]' }}">
                        Quiénes somos
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200"
                              :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-cloak x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="nav-dropdown-panel absolute top-full left-0 mt-2 w-52 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/10 rounded-xl shadow-xl shadow-black/10 dark:shadow-black/30 overflow-hidden z-50">
                        @foreach([
                            ['route'=>'public.nosotros',      'icon'=>'business',      'label'=>'Nuestra empresa'],
                            ['route'=>'public.mision-vision', 'icon'=>'track_changes', 'label'=>'Misión y Visión'],
                            ['route'=>'public.valores',       'icon'=>'favorite',      'label'=>'Nuestros valores'],
                            ['route'=>'public.equipo',        'icon'=>'groups',        'label'=>'Nuestro equipo'],
                        ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors duration-150
                                  {{ request()->routeIs($item['route']) ? 'bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] hover:text-gray-900 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[18px] text-primary-600 dark:text-primary-400 shrink-0">{{ $item['icon'] }}</span>
                            {{ $item['label'] }}
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Qué hacemos dropdown --}}
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                    <button @click="open = !open"
                            :aria-expanded="open"
                            class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 focus-brand
                                   {{ request()->routeIs('public.servicios*') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/60 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06]' }}">
                        Qué hacemos
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200"
                              :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-cloak x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="nav-dropdown-panel absolute top-full left-0 mt-2 w-56 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/10 rounded-xl shadow-xl shadow-black/10 dark:shadow-black/30 overflow-hidden z-50">
                        {{-- Header del dropdown --}}
                        <div class="px-4 py-2.5 border-b border-gray-100 dark:border-white/[0.06]">
                            <a href="{{ route('public.servicios') }}"
                               class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                                Ver todos los servicios
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        </div>
                        @foreach([
                            ['route'=>'public.servicios.desarrollo', 'icon'=>'code',        'label'=>'Desarrollo Web',    'sub'=>'ERPs y sistemas a medida'],
                            ['route'=>'public.servicios.movil',      'icon'=>'smartphone',  'label'=>'Apps Móviles',      'sub'=>'iOS y Android'],
                            ['route'=>'public.servicios.soporte',    'icon'=>'headset_mic', 'label'=>'Soporte Técnico',   'sub'=>'Mantenimiento continuo'],
                        ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-start gap-3 px-4 py-3 transition-colors duration-150
                                  {{ request()->routeIs($item['route']) ? 'bg-primary-50 dark:bg-primary-950/60' : 'hover:bg-gray-50 dark:hover:bg-white/[0.05]' }}">
                            <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-950/80 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px] text-primary-600 dark:text-primary-400">{{ $item['icon'] }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $item['label'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['sub'] }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Ejemplos dropdown --}}
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                    <button @click="open = !open"
                            :aria-expanded="open"
                            class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 focus-brand
                                   {{ request()->routeIs('public.ejemplos.*') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/60 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06]' }}">
                        Ejemplos
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200"
                              :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-cloak x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="nav-dropdown-panel absolute top-full left-0 mt-2 w-52 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-white/10 rounded-xl shadow-xl shadow-black/10 dark:shadow-black/30 overflow-hidden z-50">
                        @foreach([
                            ['route'=>'public.ejemplos.cotizaciones', 'icon'=>'request_quote', 'label'=>'Cotizaciones',   'color'=>'text-primary-600 dark:text-primary-400'],
                            ['route'=>'public.ejemplos.facturacion',  'icon'=>'receipt_long',  'label'=>'Facturación',    'color'=>'text-success'],
                            ['route'=>'public.ejemplos.inventario',   'icon'=>'inventory_2',   'label'=>'Inventario',     'color'=>'text-warning'],
                        ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors duration-150
                                  {{ request()->routeIs($item['route']) ? 'bg-primary-50 dark:bg-primary-950/60 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] hover:text-gray-900 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[18px] {{ $item['color'] }} shrink-0">{{ $item['icon'] }}</span>
                            {{ $item['label'] }}
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Portafolio --}}
                <a href="{{ route('public.ejemplos.portafolio') }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('public.ejemplos.portafolio') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/60 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06]' }}">
                    Portafolio
                </a>
            </nav>

            {{-- ── Acciones derecha ─────────────────────────────────────── --}}
            <div class="flex items-center gap-2">

                {{-- Dark mode toggle --}}
                <button @click="toggleDark()"
                        :title="darkMode ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.08] transition-all duration-200 focus-brand">
                    <span x-show="!darkMode" class="material-symbols-outlined text-[20px]">dark_mode</span>
                    <span x-show="darkMode"  class="material-symbols-outlined text-[20px]">light_mode</span>
                </button>

                {{-- Login link --}}
                <a href="{{ route('login') }}"
                   class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all duration-200">
                    <span class="material-symbols-outlined text-[18px]">login</span>
                    <span class="hidden md:inline">Iniciar sesión</span>
                </a>

                {{-- CTA Contacto --}}
                <a href="{{ route('public.contacto') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-brand text-white hover:bg-primary-700 active:bg-primary-800 transition-all duration-200 shadow-sm shadow-brand/20 hover:shadow-md hover:shadow-brand/25 focus-brand">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Contáctanos</span>
                </a>

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen"
                        :aria-expanded="mobileOpen"
                        aria-label="Abrir menú"
                        class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/[0.08] transition-all duration-200 focus-brand">
                    <span x-show="!mobileOpen" class="material-symbols-outlined text-[22px]">menu</span>
                    <span x-show="mobileOpen"  class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Mobile menu ──────────────────────────────────────────────── --}}
    <div x-cloak x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.outside="mobileOpen = false"
         class="lg:hidden border-t border-gray-200 dark:border-white/[0.06] bg-surface-light dark:bg-surface-dark max-h-[calc(100vh-4rem)] overflow-y-auto">

        <nav class="px-4 py-4 space-y-1">

            <a href="{{ route('public.home') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                      {{ request()->routeIs('public.home') ? 'bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05]' }}">
                <span class="material-symbols-outlined text-[18px]">home</span> Inicio
            </a>

            {{-- Quiénes somos accordion --}}
            <div x-data="{ open: {{ request()->routeIs('public.nosotros*','public.equipo','public.mision-vision','public.valores') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-all duration-150">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[18px]">groups</span> Quiénes somos
                    </span>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse class="mt-1 ml-4 pl-4 border-l-2 border-primary-100 dark:border-primary-900/50 space-y-0.5">
                    @foreach([
                        ['route'=>'public.nosotros',      'icon'=>'business',      'label'=>'Nuestra empresa'],
                        ['route'=>'public.mision-vision', 'icon'=>'track_changes', 'label'=>'Misión y Visión'],
                        ['route'=>'public.valores',       'icon'=>'favorite',      'label'=>'Nuestros valores'],
                        ['route'=>'public.equipo',        'icon'=>'groups',        'label'=>'Nuestro equipo'],
                    ] as $item)
                    <a href="{{ route($item['route']) }}" @click="mobileOpen = false"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150
                              {{ request()->routeIs($item['route']) ? 'text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/[0.05]' }}">
                        <span class="material-symbols-outlined text-[16px] text-primary-500">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Qué hacemos accordion --}}
            <div x-data="{ open: {{ request()->routeIs('public.servicios*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-all duration-150">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[18px]">build</span> Qué hacemos
                    </span>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse class="mt-1 ml-4 pl-4 border-l-2 border-primary-100 dark:border-primary-900/50 space-y-0.5">
                    <a href="{{ route('public.servicios') }}" @click="mobileOpen = false"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-all">
                        Ver todos los servicios
                    </a>
                    @foreach([
                        ['route'=>'public.servicios.desarrollo', 'icon'=>'code',        'label'=>'Desarrollo Web'],
                        ['route'=>'public.servicios.movil',      'icon'=>'smartphone',  'label'=>'Apps Móviles'],
                        ['route'=>'public.servicios.soporte',    'icon'=>'headset_mic', 'label'=>'Soporte Técnico'],
                    ] as $item)
                    <a href="{{ route($item['route']) }}" @click="mobileOpen = false"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150
                              {{ request()->routeIs($item['route']) ? 'text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/[0.05]' }}">
                        <span class="material-symbols-outlined text-[16px] text-primary-500">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Ejemplos accordion --}}
            <div x-data="{ open: {{ request()->routeIs('public.ejemplos.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-all duration-150">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[18px]">play_circle</span> Ejemplos
                    </span>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse class="mt-1 ml-4 pl-4 border-l-2 border-primary-100 dark:border-primary-900/50 space-y-0.5">
                    @foreach([
                        ['route'=>'public.ejemplos.cotizaciones', 'icon'=>'request_quote', 'label'=>'Cotizaciones'],
                        ['route'=>'public.ejemplos.facturacion',  'icon'=>'receipt_long',  'label'=>'Facturación'],
                        ['route'=>'public.ejemplos.inventario',   'icon'=>'inventory_2',   'label'=>'Inventario'],
                    ] as $item)
                    <a href="{{ route($item['route']) }}" @click="mobileOpen = false"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/[0.05]">
                        <span class="material-symbols-outlined text-[16px] text-primary-500">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('public.ejemplos.portafolio') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                      {{ request()->routeIs('public.ejemplos.portafolio') ? 'bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05]' }}">
                <span class="material-symbols-outlined text-[18px]">work</span> Portafolio
            </a>

            {{-- Divisor --}}
            <div class="my-2 border-t border-gray-100 dark:border-white/[0.06]"></div>

            <a href="{{ route('login') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-all duration-150">
                <span class="material-symbols-outlined text-[18px]">login</span> Iniciar sesión
            </a>

            <a href="{{ route('public.contacto') }}" @click="mobileOpen = false"
               class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold bg-brand text-white hover:bg-primary-700 active:bg-primary-800 transition-all duration-200 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">send</span> Contáctanos
            </a>
        </nav>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════════════════════════ --}}
<main class="pt-16 min-h-screen">
    @yield('content')
</main>

{{-- ══════════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════════ --}}
<footer class="bg-primary-950 dark:bg-gray-950 text-white">

    {{-- Main footer grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-8">

            {{-- Col 1 — Brand (span 2 en lg) --}}
            <div class="lg:col-span-2">
                {{-- Logo --}}
                <a href="{{ route('public.home') }}" class="inline-flex items-center gap-2.5 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-brand flex items-center justify-center shadow-lg shadow-brand/30">
                        <span class="material-symbols-outlined text-white text-[20px] ms-filled">bolt</span>
                    </div>
                    <div class="leading-tight">
                        <span class="block text-base font-black tracking-tight">JVJ Technology</span>
                        <span class="block text-[10px] font-medium text-primary-300 uppercase tracking-widest">Soluciones TI</span>
                    </div>
                </a>

                <p class="text-primary-200/70 text-sm leading-relaxed mb-6 max-w-xs">
                    Desarrollamos sistemas ERP, apps móviles y plataformas digitales completamente personalizadas para empresas que quieren crecer.
                </p>

                {{-- Social --}}
                <div class="flex items-center gap-2">
                    @foreach([
                        ['href'=>'#', 'icon'=>'fab fa-facebook-f',   'label'=>'Facebook'],
                        ['href'=>'#', 'icon'=>'fab fa-twitter',       'label'=>'Twitter'],
                        ['href'=>'#', 'icon'=>'fab fa-linkedin-in',   'label'=>'LinkedIn'],
                        ['href'=>'#', 'icon'=>'fab fa-instagram',     'label'=>'Instagram'],
                    ] as $sn)
                    <a href="{{ $sn['href'] }}" aria-label="{{ $sn['label'] }}"
                       class="w-8 h-8 rounded-lg bg-white/[0.08] hover:bg-brand flex items-center justify-center text-primary-300 hover:text-white text-xs transition-all duration-200">
                        <i class="{{ $sn['icon'] }}"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Col 2 — Empresa --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-primary-400 mb-5">Empresa</h3>
                <ul class="space-y-3">
                    @foreach([
                        ['route'=>'public.nosotros',      'label'=>'Nuestra empresa'],
                        ['route'=>'public.mision-vision', 'label'=>'Misión y Visión'],
                        ['route'=>'public.valores',       'label'=>'Nuestros valores'],
                        ['route'=>'public.equipo',        'label'=>'Nuestro equipo'],
                        ['route'=>'public.portafolio',    'label'=>'Portafolio'],
                    ] as $link)
                    <li>
{{--                         <a href="{{ route($link['route']) }}"
                           class="text-sm text-primary-200/70 hover:text-white transition-colors duration-150 hover:underline underline-offset-4 decoration-primary-600/50">
                            {{ $link['label'] }}
                        </a> --}}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 3 — Servicios --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-primary-400 mb-5">Servicios</h3>
                <ul class="space-y-3">
                    @foreach([
                        ['route'=>'public.servicios',             'label'=>'Todos los servicios'],
                        ['route'=>'public.servicios.desarrollo',  'label'=>'Desarrollo Web'],
                        ['route'=>'public.servicios.movil',       'label'=>'Apps Móviles'],
                        ['route'=>'public.servicios.soporte',     'label'=>'Soporte Técnico'],
                        ['route'=>'public.contacto',              'label'=>'Solicitar cotización'],
                    ] as $link)
                    <li>
                        <a href="{{ route($link['route']) }}"
                           class="text-sm text-primary-200/70 hover:text-white transition-colors duration-150 hover:underline underline-offset-4 decoration-primary-600/50">
                            {{ $link['label'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 4 — Contacto --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-primary-400 mb-5">Contacto</h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3 text-sm text-primary-200/70">
                        <span class="material-symbols-outlined text-[18px] text-primary-400 mt-0.5 shrink-0">location_on</span>
                        <span>C. 3 23, Bonanza, 86030<br>Villahermosa, Tabasco, MX</span>
                    </li>
                    <li>
                        <a href="tel:+529931234567" class="flex items-center gap-3 text-sm text-primary-200/70 hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[18px] text-primary-400 shrink-0">call</span>
                            +52 (993) 123-4567
                        </a>
                    </li>
                    <li>
                        <a href="mailto:info@jvjtechnology.com" class="flex items-center gap-3 text-sm text-primary-200/70 hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[18px] text-primary-400 shrink-0">email</span>
                            info@jvjtechnology.com
                        </a>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-primary-200/70">
                        <span class="material-symbols-outlined text-[18px] text-primary-400 mt-0.5 shrink-0">schedule</span>
                        <span>Lun–Vie 9:00–18:00<br>Sáb 9:00–13:00</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Footer bottom bar --}}
    <div class="border-t border-white/[0.08]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-primary-300/60">
                © {{ date('Y') }} JVJ Technology. Todos los derechos reservados.
            </p>
            <div class="flex items-center gap-4">
                <a href="#" class="text-xs text-primary-300/60 hover:text-white transition-colors">Privacidad</a>
                <span class="w-px h-3 bg-primary-700"></span>
                <a href="#" class="text-xs text-primary-300/60 hover:text-white transition-colors">Términos</a>
                <span class="w-px h-3 bg-primary-700"></span>
                <a href="{{ route('login') }}" class="text-xs text-primary-300/60 hover:text-white transition-colors">Acceso clientes</a>
            </div>
        </div>
    </div>
</footer>

{{-- ══════════════════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════════════════ --}}
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 600, once: true, offset: 80, easing: 'ease-out-cubic' });

    function layoutApp() {
        return {
            darkMode: localStorage.getItem('darkMode') === 'true',
            mobileOpen: false,
            scrolled: false,

            init() {
                // Escuchar scroll para shadow en navbar
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 8;
                }, { passive: true });

                // Aplicar dark mode al <html>
                this.$watch('darkMode', val => {
                    localStorage.setItem('darkMode', val);
                    document.documentElement.classList.toggle('dark', val);
                });
            },

            toggleDark() {
                this.darkMode = !this.darkMode;
            }
        };
    }
</script>

@stack('scripts')
</body>
</html>
