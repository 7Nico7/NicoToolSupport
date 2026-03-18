@props([
    'title'          => 'Sistema de Demostración',
    'module'         => 'generic',
    'showBackButton' => true,
    'backAction'     => 'exitFullscreen',
    'userName'       => 'Usuario Demo',
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

<header class="sticky top-0 z-50 h-14 flex items-center justify-between px-5 bg-brand dark:bg-surface border-b border-white/10 dark:border-white/[0.08] shadow-lg shadow-brand/10 dark:shadow-none">

    {{-- Left: icon + title --}}
    <div class="flex items-center gap-3">
        @if($showBackButton)
            <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all mr-1 sm:hidden">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </button>
        @endif

        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center hidden sm:flex">
                <span class="material-symbols-outlined text-white text-[20px]" style="font-variation-settings:'FILL' 1">
                    {{ $msIcon }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="font-black text-white text-sm tracking-tight leading-none">
                    {{ $title }}
                </span>
                <span class="text-[10px] text-white/60 font-bold uppercase tracking-widest sm:hidden">
                    JVJ Tech
                </span>
            </div>
        </div>

        <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 text-white text-[10px] font-black uppercase tracking-wider border border-white/10">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Demo Live
        </span>
    </div>

    {{-- Right: user dropdown + help --}}
    <div class="flex items-center gap-2 sm:gap-3">

        {{-- USER MENU --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="flex items-center gap-2 px-2 sm:px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm transition-all border border-white/5">

                <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center overflow-hidden">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                </div>

                <span class="hidden sm:inline font-bold tracking-tight">
                    {{ $userName }}
                </span>

                <span class="material-symbols-outlined text-[16px] transition-transform" :class="open ? 'rotate-180' : ''">
                    expand_more
                </span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="absolute right-0 mt-2 w-56 bg-surface border border-gray-200 dark:border-white/[0.1] rounded-2xl shadow-2xl py-2 z-50 overflow-hidden"
                x-cloak>

                <div class="px-4 py-3 border-b border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02]">
                    <p class="font-black text-gray-900 dark:text-white text-sm">{{ $userName }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Administrador del Sistema</p>
                </div>

                <div class="py-1">
                    @foreach([
                        ['person', 'Mi Perfil', '#'],
                        ['settings', 'Configuración', '#'],
                        ['history', 'Log de Actividad', '#']
                    ] as [$icon, $label, $href])
                    <a href="{{ $href }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:bg-brand hover:text-white dark:hover:bg-brand transition-all group">
                        <span class="material-symbols-outlined text-[18px] text-gray-400 group-hover:text-white">
                            {{ $icon }}
                        </span>
                        <span class="font-semibold">{{ $label }}</span>
                    </a>
                    @endforeach
                </div>

                <div class="h-px bg-gray-100 dark:bg-white/[0.06] my-1"></div>

                <a href="/" class="flex items-center gap-3 px-4 py-2.5 text-sm text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span class="font-bold">Salir del Demo</span>
                </a>
            </div>
        </div>

        {{-- HELP BUTTON --}}
        <div x-data="{ open: false }">
            <button @click="open = true"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/25 text-white transition-all border border-white/5">
                <span class="material-symbols-outlined text-[20px]">help</span>
            </button>

            {{-- HELP MODAL --}}
            <template x-teleport="body">
                <div x-show="open"
                    class="fixed inset-0 z-[1000] flex items-center justify-center p-4"
                    x-cloak>

                    {{-- Backdrop --}}
                    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm" @click="open = false"></div>

                    {{-- Modal Content --}}
                    <div x-show="open"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="relative w-full max-w-sm bg-surface rounded-3xl border border-white/[0.1] shadow-2xl p-6 overflow-hidden">

                        <div class="absolute top-0 left-0 w-full h-1.5 bg-brand"></div>

                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-brand/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-brand text-[20px]">lightbulb</span>
                                </div>
                                <h3 class="font-black text-gray-900 dark:text-white uppercase tracking-tight">Tips del Demo</h3>
                            </div>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        <ul class="space-y-4 mb-8">
                            @foreach([
                                ['touch_app', 'Interacción Total', 'Todos los botones son funcionales en este entorno controlado.'],
                                ['dark_mode', 'Modo Oscuro', 'El sistema detecta automáticamente la preferencia de tu sistema.'],
                                ['security', 'Datos Seguros', 'Esta es una instancia aislada; tus pruebas no afectan datos reales.']
                            ] as [$icon, $title, $text])
                            <li class="flex gap-4">
                                <span class="material-symbols-outlined text-brand shrink-0">{{ $icon }}</span>
                                <div>
                                    <p class="text-sm font-black text-gray-800 dark:text-white leading-none mb-1">{{ $title }}</p>
                                    <p class="text-[12px] text-gray-500 dark:text-gray-400 leading-snug">{{ $text }}</p>
                                </div>
                            </li>
                            @endforeach
                        </ul>

                        <button @click="open = false" class="w-full py-3 rounded-xl bg-brand text-white text-sm font-black uppercase tracking-widest hover:bg-brand/90 transition-all shadow-lg shadow-brand/20">
                            ¡Comenzar!
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</header>
