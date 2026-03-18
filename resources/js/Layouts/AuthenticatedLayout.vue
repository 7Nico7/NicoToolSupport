<script setup>
import { ref } from 'vue';
import { useDarkMode } from '@/shared/composables/useDarkMode';
// CORRECCIÓN: Añadimos 'router' aquí dentro de las llaves
import { Link, usePage, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/shared/components/ApplicationLogo.vue';
import Dropdown from '@/shared/components/Dropdown.vue';
import DropdownLink from '@/shared/components/DropdownLink.vue';

const { isDark, toggleDark } = useDarkMode();
const isCollapsed = ref(false);
const page = usePage();

const openMenus = ref({
    tickets: false,
    knowledge: false,
});

const toggleMenu = (menu) => {
    if (isCollapsed.value) isCollapsed.value = false;
    openMenus.value[menu] = !openMenus.value[menu];
};

const isActive = (routeName) => route().current(routeName);

const handleLogout = () => {
    console.log('Intentando cerrar sesión...'); // Esto aparecerá en tu consola (F12)
    router.post(route('logout'), {}, {
        onSuccess: () => {
            // Esto asegura que salgas completamente al login sin parpadeos
            window.location.href = route('login');
        }
    });
};
</script>


<template>
    <div class="relative flex h-screen w-full overflow-hidden bg-background-light dark:bg-background-dark">

        <!-- ── Sidebar ──────────────────────────────────────────────────────── -->
        <aside :class="isCollapsed ? 'w-20' : 'w-72'"
            class="flex flex-col h-screen bg-surface-light dark:bg-surface-dark border-r border-slate-200 dark:border-slate-800 transition-[width] duration-300 ease-in-out shrink-0 z-50 overflow-hidden">
            <!-- Logo -->
            <div class="flex h-16 items-center gap-2 px-4 border-b border-slate-100 dark:border-slate-800/50 shrink-0">
                <div class="flex items-center justify-center rounded-md h-12 w-12 dark:bg-brand shrink-0">
                    <ApplicationLogo class="h-12 w-12 text-white fill-current dark:brightness-0 dark:invert" />
                </div>
                <div :class="isCollapsed ? 'opacity-0 -translate-x-4 pointer-events-none' : 'opacity-100 translate-x-0'"
                    class="flex flex-col transition-all duration-300 whitespace-nowrap">
                    <h1
                        class="text-slate-900 dark:text-white text-sm font-black uppercase tracking-widest leading-none">
                        Soporte</h1>
                    <span class="text-brand text-[10px] font-bold uppercase tracking-[0.2em]">Technology</span>
                </div>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 h-0 overflow-y-auto p-4 space-y-1 custom-scrollbar overflow-x-hidden">

                <!-- Dashboard -->
                <Link :href="route('dashboard')"
                    :class="isActive('dashboard') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group">
                    <span
                        class="material-symbols-outlined shrink-0 transition-transform group-hover:scale-110">dashboard</span>
                    <span :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100'"
                        class="text-sm font-semibold whitespace-nowrap transition-all duration-300 overflow-hidden">Dashboard</span>
                </Link>


                <!-- Compañias -->
                <Link :href="route('companies.index')"
                    :class="isActive('companies.index') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group">
                    <span
                        class="material-symbols-outlined shrink-0 transition-transform group-hover:scale-110">folder</span>
                    <span :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100'"
                        class="text-sm font-semibold whitespace-nowrap transition-all duration-300 overflow-hidden">Empresas</span>
                </Link>


                <!-- Proyectos -->
                <Link :href="route('projects.index')"
                    :class="isActive('projects.index') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group">
                    <span
                        class="material-symbols-outlined shrink-0 transition-transform group-hover:scale-110">folder</span>
                    <span :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100'"
                        class="text-sm font-semibold whitespace-nowrap transition-all duration-300 overflow-hidden">Proyectos</span>
                </Link>

                <!-- Usuarios -->
                <Link :href="route('users.index')"
                    :class="isActive('users.index') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group">
                    <span
                        class="material-symbols-outlined shrink-0 transition-transform group-hover:scale-110">group</span>
                    <span :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100'"
                        class="text-sm font-semibold whitespace-nowrap transition-all duration-300 overflow-hidden">Usuarios</span>
                </Link>

                <!-- ── Tickets (desplegable) ───────────────────────────────── -->
                <div class="space-y-0.5">
                    <button @click="toggleMenu('tickets')"
                        :class="(isActive('kanban') || isActive('gantt')) ? 'text-brand bg-brand/5' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5'"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined shrink-0">confirmation_number</span>
                            <span :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100'"
                                class="text-sm font-semibold whitespace-nowrap transition-all duration-300 overflow-hidden">Tickets</span>
                        </div>
                        <span
                            :class="[isCollapsed ? 'opacity-0 w-0' : 'opacity-100', openMenus.tickets ? 'rotate-180' : '']"
                            class="material-symbols-outlined text-sm transition-all duration-300">expand_more</span>
                    </button>

                    <Transition enter-active-class="transition-all duration-200 ease-out"
                        leave-active-class="transition-all duration-150 ease-in"
                        enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
                        <div v-show="openMenus.tickets && !isCollapsed" class="ml-9 space-y-0.5">
                            <!-- Kanban board -->
                            <Link :href="route('kanban')"
                                :class="isActive('kanban') ? 'text-brand font-bold' : 'text-slate-500 hover:text-brand'"
                                class="flex items-center gap-2 py-2 text-xs font-medium transition-colors whitespace-nowrap">
                                <span class="material-symbols-outlined text-sm">view_kanban</span>
                                Kanban
                            </Link>
                            <!-- Diagrama de Gantt -->
                            <Link :href="route('gantt')"
                                :class="isActive('gantt') ? 'text-brand font-bold' : 'text-slate-500 hover:text-brand'"
                                class="flex items-center gap-2 py-2 text-xs font-medium transition-colors whitespace-nowrap">
                                <span class="material-symbols-outlined text-sm">date_range</span>
                                Diagrama Gantt
                            </Link>
                        </div>
                    </Transition>
                </div>

                <!-- Clientes -->
                <Link href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-all group">
                    <span
                        class="material-symbols-outlined shrink-0 group-hover:text-brand transition-colors">business_center</span>
                    <span :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100'"
                        class="text-sm font-semibold whitespace-nowrap transition-all duration-300 overflow-hidden">Clientes</span>
                </Link>

                <!-- ── Base de Conocimiento (desplegable) ──────────────────── -->
                <div class="space-y-0.5">
                    <button @click="toggleMenu('knowledge')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined shrink-0">menu_book</span>
                            <span :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100'"
                                class="text-sm font-semibold whitespace-nowrap transition-all duration-300 overflow-hidden">Base
                                de Conocimiento</span>
                        </div>
                        <span
                            :class="[isCollapsed ? 'opacity-0 w-0' : 'opacity-100', openMenus.knowledge ? 'rotate-180' : '']"
                            class="material-symbols-outlined text-sm transition-all duration-300">expand_more</span>
                    </button>

                    <Transition enter-active-class="transition-all duration-200 ease-out"
                        leave-active-class="transition-all duration-150 ease-in"
                        enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
                        <div v-show="openMenus.knowledge && !isCollapsed" class="ml-9 space-y-0.5">
                            <Link href="#"
                                class="flex items-center gap-2 py-2 text-xs font-medium text-slate-500 hover:text-brand transition-colors whitespace-nowrap">
                                <span class="material-symbols-outlined text-sm">article</span>Artículos
                            </Link>
                            <Link href="#"
                                class="flex items-center gap-2 py-2 text-xs font-medium text-slate-500 hover:text-brand transition-colors whitespace-nowrap">
                                <span class="material-symbols-outlined text-sm">category</span>Categorías
                            </Link>
                            <Link href="#"
                                class="flex items-center gap-2 py-2 text-xs font-medium text-slate-500 hover:text-brand transition-colors whitespace-nowrap">
                                <span class="material-symbols-outlined text-sm">quiz</span>FAQ
                            </Link>
                        </div>
                    </Transition>
                </div>

            </nav>

            <!-- ── Perfil + logout ──────────────────────────────────────────── -->
            <!--
                CORRECCIÓN: antes tenía v-if="true" hardcodeado, lo que hacía
                que el bloque con el botón de logout siempre intentara mostrarse
                incluso con el sidebar colapsado (w-20), quedando aplastado por overflow.

                Ahora:
                  - Sidebar expandido → avatar + nombre + botón logout
                  - Sidebar colapsado → solo el avatar (tooltip con nombre en title)
            -->
            <div
                class="p-4 border-t border-slate-100 dark:border-slate-800 bg-surface-light/50 dark:bg-surface-dark/10 shrink-0">

                <!-- Modo expandido -->
                <div v-if="!isCollapsed" class="flex items-center gap-3">
                    <!-- Avatar -->
                    <div
                        class="h-10 w-10 rounded-xl bg-brand text-white flex items-center justify-center font-bold shadow-lg shadow-brand/20 shrink-0 text-sm">
                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </div>
                    <!-- Nombre + rol -->
                    <div class="flex flex-col flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-[10px] text-brand font-bold uppercase tracking-tighter capitalize">
                            {{ $page.props.auth.user.role ?? 'Agente' }}
                        </p>
                    </div>
                    <!-- Botón logout -->

<button
    type="button"
    @click="handleLogout"
    class="shrink-0 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-danger transition-colors"
>
    <span class="material-symbols-outlined">logout</span>
</button>

                </div>

                <!-- Modo colapsado: solo avatar centrado con tooltip nativo -->
                <div v-else class="flex justify-center">
                    <div :title="`${$page.props.auth.user.name} — Cerrar sesión`"
                        class="h-10 w-10 rounded-xl bg-brand text-white flex items-center justify-center font-bold shadow-lg shadow-brand/20 text-sm cursor-default">
                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </div>
                </div>
            </div>
        </aside>

        <!-- ── Contenido principal ──────────────────────────────────────────── -->
        <div class="flex flex-1 flex-col overflow-hidden">

            <!-- Header -->
            <header
                class="flex h-16 items-center justify-between px-8 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 z-40">
                <div class="flex items-center gap-4">
                    <!-- Toggle sidebar -->
                    <button @click="isCollapsed = !isCollapsed"
                        class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500 transition-all active:scale-95">
                        <span class="material-symbols-outlined">{{ isCollapsed ? 'menu_open' : 'menu' }}</span>
                    </button>

                    <!-- Breadcrumb -->
                    <div
                        class="hidden sm:flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
                        <span>Helpdesk</span>
                        <span class="material-symbols-outlined text-xs">chevron_right</span>
                        <span class="text-slate-900 dark:text-white">{{ $page.component.split('/').pop() }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Dark mode toggle -->
                    <button @click="toggleDark"
                        class="p-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-500 hover:text-brand transition-all active:rotate-12">
                        <span class="material-symbols-outlined text-xl">{{ isDark ? 'light_mode' : 'dark_mode' }}</span>
                    </button>

                    <div class="h-8 w-px bg-slate-200 dark:bg-slate-800 mx-1" />

                    <!-- Usuario → dropdown con opciones -->
                    <Dropdown align="right" width="56">
                        <template #trigger>
                            <button
                                class="flex items-center gap-2.5 px-2 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-all focus:outline-none group">
                                <!-- Avatar -->
                                <div
                                    class="h-8 w-8 rounded-lg bg-brand text-white flex items-center justify-center text-xs font-black shrink-0 shadow shadow-brand/30">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <!-- Nombre + rol -->
                                <div class="hidden md:flex flex-col items-start leading-tight">
                                    <span
                                        class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-brand transition-colors">
                                        {{ $page.props.auth.user.name }}
                                    </span>
                                    <span
                                        class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter capitalize">
                                        {{ $page.props.auth.user.role ?? 'Agente' }}
                                    </span>
                                </div>
                                <span
                                    class="material-symbols-outlined text-sm text-slate-400 group-hover:rotate-180 transition-transform duration-300 hidden md:block">expand_more</span>
                            </button>
                        </template>

                        <template #content>
                            <!-- Info del usuario (no clickeable) -->
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{
                                    $page.props.auth.user.name }}</p>
                                <p class="text-xs text-slate-400 capitalize">{{ $page.props.auth.user.role ?? 'Agente'
                                    }}</p>
                            </div>

                            <!-- Mi perfil -->
                            <DropdownLink :href="route('profile.edit')">
                                <span
                                    class="material-symbols-outlined text-base mr-2.5 text-slate-400">manage_accounts</span>
                                Mi Perfil
                            </DropdownLink>

                            <div class="border-t border-slate-100 dark:border-slate-700 my-1" />

                            <!-- Cerrar sesión -->
<button
    @click="handleLogout"
    class="flex w-full items-center px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-left"
>
    <span class="material-symbols-outlined mr-2.5 text-danger">logout</span>
    Cerrar sesión
</button>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar">
                <div class="max-w-[1400px] mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-200 dark:bg-slate-800 rounded-full;
}

aside {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

aside::-webkit-scrollbar {
    display: none;
}
</style>
