{{-- file: resources/views/layouts/demo.blade.php --}}
<!DOCTYPE html>
<html lang="es"
      x-data="demoApp()"
      :class="{ 'dark': darkMode }"
      class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Demo') — JVJ Technology</title>

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Tailwind CSS v4 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @custom-variant dark (&:where(.dark, .dark *));

        @theme {
            --color-brand: #28a745;
           --color-primary-700: #1e7e34;
           --color-primary-200: #a1e7ae;

            /* Colores de Superficie para el modo oscuro */
            --color-surface-light: #ffffff;
            --color-surface-dark: #111827;

            --color-background-light: #f3f4f6;
            --color-background-dark: #0f172a;

            --color-warning: #f59e0b;
            --color-danger: #ef4444;
        }

        @layer utilities {
            .bg-surface { @apply bg-surface-light dark:bg-surface-dark; }
            .bg-app { @apply bg-background-light dark:bg-background-dark; }
        }
    </style>

    <style>
        [x-cloak] { display: none !important; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0; vertical-align: middle; }
        .ms-filled { font-variation-settings: 'FILL' 1; }
        #demo-sidebar, #demo-main { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
    @stack('styles')
</head>

<body class="font-sans bg-app text-gray-900 dark:text-gray-100 antialiased h-full">

    {{-- SIDEBAR --}}
    <aside id="demo-sidebar"
           :class="{ 'w-[260px]': !sidebarCollapsed, 'w-[68px]': sidebarCollapsed, 'translate-x-0': mobileSidebarOpen, '-translate-x-full lg:translate-x-0': !mobileSidebarOpen }"
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-surface border-r border-gray-200 dark:border-white/[0.08] shadow-sm overflow-hidden">

        {{-- Header Sidebar --}}
        <div class="flex items-center justify-between shrink-0 h-14 px-4 bg-gradient-to-r from-primary-700 to-brand">
            <a href="{{ route('public.home') }}" class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                    <span class="material-symbols-outlined text-white text-[18px] ms-filled">bolt</span>
                </div>
                <div x-show="!sidebarCollapsed" class="leading-tight">
                    <span class="block text-sm font-black text-white tracking-tight">JVJ Technology</span>
                    <span class="block text-[10px] font-medium text-primary-200 uppercase tracking-widest">Modo Demo</span>
                </div>
            </a>
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex text-white/80 hover:text-white">
                <span class="material-symbols-outlined text-[18px]" :class="sidebarCollapsed ? 'rotate-180' : ''">chevron_left</span>
            </button>
        </div>

        {{-- Banner Demo --}}
        <div x-show="!sidebarCollapsed" class="flex items-center gap-2 px-4 py-2 bg-warning/10 border-b border-warning/20 dark:border-white/[0.05]">
            <span class="material-symbols-outlined text-warning text-[14px] ms-filled">info</span>
            <span class="text-[11px] font-bold text-warning">Versión de demostración</span>
        </div>

        {{-- NAVEGACIÓN PRINCIPAL --}}
        <nav class="flex-1 overflow-y-auto py-4 space-y-0.5 px-3">

            {{-- Dashboard --}}
            <a href="{{ route('demo.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('demo.dashboard') ? 'bg-brand/10 dark:bg-brand/20 text-brand font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.05]' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('demo.dashboard') ? 'ms-filled' : '' }}">dashboard</span>
                <span x-show="!sidebarCollapsed">Dashboard</span>
            </a>

            <div class="pt-4 pb-2 px-3" x-show="!sidebarCollapsed">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Módulos</p>
            </div>

            {{-- Clientes --}}
            <a href="{{ route('demo.clientes') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('demo.clientes*') ? 'bg-brand/10 dark:bg-brand/20 text-brand font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.05]' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('demo.clientes*') ? 'ms-filled' : '' }}">group</span>
                <span x-show="!sidebarCollapsed">Clientes</span>
            </a>

            {{-- Inventario (con Submenú) --}}
            <div x-data="{ open: {{ request()->routeIs('demo.inventario*') ? 'true' : 'false' }} }">
                <button @click="if(sidebarCollapsed) { sidebarCollapsed = false; open = true; } else { open = !open; }"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        {{ request()->routeIs('demo.inventario*') ? 'bg-brand/10 dark:bg-brand/20 text-brand font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.05]' }}">
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('demo.inventario*') ? 'ms-filled' : '' }}">inventory_2</span>
                    <span x-show="!sidebarCollapsed" class="flex-1 text-left">Inventario</span>
                    <span x-show="!sidebarCollapsed" class="material-symbols-outlined text-[16px] transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open && !sidebarCollapsed" x-cloak class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 dark:border-white/[0.05] space-y-1">
                    <a href="{{ route('demo.inventario') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.05] {{ request()->routeIs('demo.inventario') ? 'text-brand font-bold' : 'text-gray-500 dark:text-gray-400' }}">Listado</a>
                    <a href="{{ route('demo.inventario.existencia') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.05] {{ request()->routeIs('demo.inventario.existencia') ? 'text-brand font-bold' : 'text-gray-500 dark:text-gray-400' }}">Existencia</a>
                </div>
            </div>

            {{-- Cotizaciones --}}
            <a href="{{ route('demo.cotizaciones') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('demo.cotizaciones*') ? 'bg-brand/10 dark:bg-brand/20 text-brand font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.05]' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('demo.cotizaciones*') ? 'ms-filled' : '' }}">request_quote</span>
                <span x-show="!sidebarCollapsed">Cotizaciones</span>
            </a>

            {{-- Facturación --}}
            <a href="{{ route('demo.facturacion') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('demo.facturacion*') ? 'bg-brand/10 dark:bg-brand/20 text-brand font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.05]' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('demo.facturacion*') ? 'ms-filled' : '' }}">receipt_long</span>
                <span x-show="!sidebarCollapsed">Facturación</span>
            </a>

            {{-- SECCIÓN REPORTES (DISABLED) --}}
            <div class="pt-4 pb-2 px-3" x-show="!sidebarCollapsed">
                <div class="flex items-center gap-2">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Reportes</p>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-warning/15 text-warning">Demo</span>
                </div>
            </div>
            <button onclick="showNotAvailable('Estadísticas')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-400 dark:text-gray-600 opacity-60 cursor-not-allowed">
                <span class="material-symbols-outlined text-[20px]">bar_chart</span>
                <span x-show="!sidebarCollapsed">Estadísticas</span>
            </button>
            <button onclick="showNotAvailable('Exportar datos')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-400 dark:text-gray-600 opacity-60 cursor-not-allowed">
                <span class="material-symbols-outlined text-[20px]">download</span>
                <span x-show="!sidebarCollapsed">Exportar datos</span>
            </button>

            {{-- SECCIÓN SISTEMA --}}
            <div class="pt-4 pb-2 px-3" x-show="!sidebarCollapsed">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Sistema</p>
            </div>
            <button onclick="showNotAvailable('Configuración')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-400 dark:text-gray-600 opacity-60 cursor-not-allowed">
                <span class="material-symbols-outlined text-[20px]">settings</span>
                <span x-show="!sidebarCollapsed">Configuración</span>
            </button>

            {{-- SALIR --}}
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-danger hover:bg-danger/10 transition-colors mt-2">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span x-show="!sidebarCollapsed">Salir del demo</span>
            </a>
        </nav>
    </aside>

    {{-- CONTENIDO --}}
    <div id="demo-main"
         :class="sidebarCollapsed ? 'lg:ml-[68px]' : 'lg:ml-[260px]'"
         class="min-h-screen flex flex-col">

        {{-- Header Topbar --}}
        <header class="sticky top-0 z-30 h-14 flex items-center justify-between px-6 bg-surface border-b border-gray-200 dark:border-white/[0.08]">
            <div class="flex items-center gap-4">
                <button @click="mobileSidebarOpen = true" class="lg:hidden text-gray-500">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="text-xs font-black dark:text-white uppercase tracking-wider">@yield('title', 'Demo - Sistema de Cotizaciones')</h1>
            </div>

            <div class="flex items-center gap-4">
                <button @click="toggleDark()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-white/[0.1] text-gray-500 dark:text-gray-400 transition-colors">
                    <span x-show="!darkMode" class="material-symbols-outlined text-[20px]">dark_mode</span>
                    <span x-show="darkMode" class="material-symbols-outlined text-[20px]" x-cloak>light_mode</span>
                </button>

                <div class="flex items-center gap-3 pl-4 border-l border-gray-200 dark:border-white/[0.1]">
                    <div class="w-8 h-8 rounded-full bg-brand flex items-center justify-center text-white text-[10px] font-bold">UD</div>
                    <span class="hidden md:block text-xs font-bold dark:text-gray-200">Usuario Demo</span>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    {{-- Overlay móvil --}}
    <div x-show="mobileSidebarOpen" x-transition.opacity @click="mobileSidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function demoApp() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                mobileSidebarOpen: false,
                init() { this.applyTheme(); },
                toggleDark() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    this.applyTheme();
                },
                applyTheme() {
                    if (this.darkMode) document.documentElement.classList.add('dark');
                    else document.documentElement.classList.remove('dark');
                }
            }
        }

        function showNotAvailable(feature) {
            Swal.fire({
                title: 'No disponible',
                text: `La función ${feature} está deshabilitada en la demo.`,
                icon: 'info',
                confirmButtonColor: '#059669'
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
