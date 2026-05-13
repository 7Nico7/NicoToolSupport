<script setup>
// resources/js/shared/components/PageHeader.vue
//
// Header reutilizable para páginas de formulario y listados.
// Muestra opcionalmente un botón "Volver", título y descripción.
//
// USO básico:
//   <PageHeader title="Nuevo Helpdesk" description="Define el nombre..." />
//
// CON botón volver:
//   <PageHeader
//       title="Editar Helpdesk"
//       description="Modifica los datos."
//       back-label="Volver a Helpdesks"
//       :back-href="route('helpdesks.index')"
//   />
//
// CON slot para acciones a la derecha (ej. botón "Nuevo"):
//   <PageHeader title="Helpdesks" description="Gestiona las mesas de ayuda.">
//       <template #actions>
//           <PrimaryButton icon="add">Nuevo</PrimaryButton>
//       </template>
//   </PageHeader>

import { router } from '@inertiajs/vue3'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'

defineProps({
    // Texto del encabezado principal
    title:       { type: String, required: true },
    // Subtítulo / descripción debajo del título
    description: { type: String, default: '' },
    // Texto del botón de regreso — si no se pasa, el botón no se muestra
    backLabel:   { type: String, default: '' },
    // Ruta/URL a la que navega el botón de regreso
    backHref:    { type: String, default: '' },
})
</script>

<template>
    <div class="flex items-start justify-between flex-wrap gap-4">

        <!-- Izquierda: título + descripción -->
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ title }}
            </h1>
            <p v-if="description" class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ description }}
            </p>
        </div>

        <!-- Derecha: botón volver y/o acciones — alineados verticalmente al centro del título -->
        <div class="flex items-center gap-3 flex-wrap">
            <slot name="actions" />

            <SecondaryButton
                v-if="backLabel"
                type="button"
                icon="arrow_back"
                size="sm"
                @click="router.visit(backHref)"
            >
                {{ backLabel }}
            </SecondaryButton>
        </div>

    </div>
</template>
