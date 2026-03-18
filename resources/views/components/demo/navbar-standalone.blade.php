@props([
    'title' => 'Sistema de Cotizaciones',
    'module' => 'cotizacion',
    'showBackButton' => true,
    'backAction' => 'exitFullscreen',
    'userName' => 'Usuario Demo',
])

@php
$iconMap = [
    'cotizacion' => 'request_quote',
    'inventario' => 'inventory_2',
    'factura'    => 'receipt_long',
    'clientes'   => 'group',
    'generic'    => 'dashboard',
];

$msIcon = $iconMap[$module] ?? 'dashboard';
@endphp

<header x-data="{ userMenu: false, helpModal: false }"
        class="sticky top-0 z-50 h-14 flex items-center justify-between px-5 bg-brand dark:bg-surface border-b border-white/10 dark:border-white/[0.08] shadow-lg shadow-brand/10 dark:shadow-none">

    {{-- LEFT: Brand & Title --}}
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-[20px]" style="font-variation-settings:'FILL' 1">
                    {{ $msIcon }}
                </span>
            </div>
            <span class="font-black text-white text-sm tracking-tight">
                {{ $title }}
            </span>
        </div>

        <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 text-white text-[10px] font-black uppercase tracking-wider border border-white/10">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Demo Live
        </span>
    </div>

    {{-- RIGHT: Actions --}}
    <div class="flex items-center gap-3">

        {{-- USER DROPDOWN --}}
        <div class="relative">
            <button @click="userMenu = !userMenu" @click.away="userMenu = false"
                class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-sm transition-all border border-white/5">

                <span class="material-symbols-outlined text-[18px]">account_circle</span>
                <span class="hidden sm:inline font-bold">{{ $userName }}</span>
                <span class="material-symbols-outlined text-[16px] transition-transform" :class="userMenu ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="userMenu"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 mt-2 w-52 bg-surface border border-gray-200 dark:border-white/[0.1] rounded-2xl shadow-xl py-1.5 z-50 overflow-hidden"
                 x-cloak>

                <div class="px-4 py-3 border-b border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02]">
                    <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $userName }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">usuario@demo.com</p>
                </div>

                <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand hover:text-white transition-colors group">
                    <span class="material-symbols-outlined text-[18px] text-gray-400 group-hover:text-white">person</span>
                    Mi Perfil
                </a>
                <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand hover:text-white transition-colors group">
                    <span class="material-symbols-outlined text-[18px] text-gray-400 group-hover:text-white">settings</span>
                    Configuración
                </a>

                <div class="h-px bg-gray-100 dark:bg-white/[0.06] my-1"></div>

                <a href="{{ route('public.home') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span class="font-bold">Salir del Demo</span>
                </a>
            </div>
        </div>

        {{-- HELP BUTTON --}}
        <button @click="helpModal = true"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/15 hover:bg-white/25 text-white transition-all border border-white/5">
            <span class="material-symbols-outlined text-[20px]">help</span>
        </button>

        {{-- HELP MODAL (Teleported) --}}
        <template x-teleport="body">
            <div x-show="helpModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4" x-cloak>
                <div x-show="helpModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm" @click="helpModal = false"></div>

                <div x-show="helpModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     class="relative w-full max-w-sm bg-surface rounded-3xl border border-white/[0.1] shadow-2xl p-6 overflow-hidden">

                    <div class="absolute top-0 left-0 w-full h-1.5 bg-brand"></div>

                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-black text-gray-900 dark:text-white uppercase tracking-tight">Ayuda del Demo</h3>
                        <button @click="helpModal = false" class="text-gray-400 hover:text-brand transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <ul class="space-y-4 mb-8">
                        @foreach([
                            ['info', 'Demo Interactivo', 'Explora todas las funciones sin miedo.'],
                            ['view_column', 'Columnas Dinámicas', 'Personaliza tu vista de tabla.'],
                            ['drag_indicator', 'Reordenar', 'Arrastra elementos para organizar.'],
                            ['table_view', 'Exportación', 'Descarga reportes en Excel/CSV.']
                        ] as [$icon, $label, $desc])
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-brand text-[20px]">{{ $icon }}</span>
                            <div class="text-sm">
                                <p class="font-bold text-gray-800 dark:text-white leading-none">{{ $label }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $desc }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    <button @click="helpModal = false" class="w-full py-3 rounded-xl bg-brand text-white text-sm font-black uppercase tracking-widest hover:bg-brand/90 transition-all shadow-lg shadow-brand/20">
                        Entendido
                    </button>
                </div>
            </div>
        </template>
    </div>
</header>

<script>
    // Mantengo esta función por compatibilidad con tu backAction
    window.exitFullscreen = function () {
        if (document.fullscreenElement) {
            document.exitFullscreen()
        }
    }
</script>
