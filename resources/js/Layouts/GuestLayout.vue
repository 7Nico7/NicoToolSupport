<script setup>
import ApplicationLogo from '@/shared/components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center px-4 py-8 sm:py-12 transition-all duration-500
               bg-gradient-to-b from-brand/30 via-surface-light to-gray-200
               dark:from-brand/20 dark:via-surface-dark dark:to-gray-900"
    >
        <div class="w-full max-w-md overflow-hidden bg-white/90 dark:bg-surface-dark/90 backdrop-blur-xl p-6 shadow-2xl rounded-3xl md:p-8 border border-gray-200 dark:border-white/10 ring-1 ring-black/5 relative z-10">

            <div class="mb-8 flex flex-col items-center justify-center">
                <Link href="/" class="flex flex-col items-center group">
                    <ApplicationLogo
                        class="h-24 w-auto fill-current text-brand transition-transform group-hover:scale-105 duration-300" />

                    <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white uppercase">
                        Sistema de <span class="text-brand">Soporte</span>
                    </h1>
                    <div class="h-1.5 w-12 bg-brand rounded-full mt-1"></div>
                </Link>
            </div>

            <slot />
        </div>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400 font-medium">
            &copy; {{ new Date().getFullYear() }} Helpdesk. Todos los derechos reservados.
        </p>
    </div>
</template>
