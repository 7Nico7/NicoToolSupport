<script setup>
// resources/js/Pages/Projects/Form.vue
import { computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/shared/components/InputLabel.vue'
import TextInput from '@/shared/components/TextInput.vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'

const props = defineProps({
    project: { type: Object, default: null },
    company: { type: Object, default: null },
    can:     { type: Object, default: () => ({}) },
})

const isEditing = computed(() => props.project !== null)

const form = useForm({
    name:        props.project?.name        ?? '',
    email:       props.project?.email       ?? '',
    description: props.project?.description ?? '',
    is_active:   props.project?.is_active   ?? true,
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('projects.update', props.project.id), { preserveScroll: true })
    } else {
        form.post(route('projects.store'))
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar proyecto' : 'Nuevo proyecto'" />

    <AuthenticatedLayout>
        <div class="max-w-full mx-auto flex flex-col gap-5">

            <div class="flex flex-col gap-2">
                <!-- ✅ SecondaryButton en lugar de <Link> nativo con estilos manuales -->
                <SecondaryButton
                    type="button"
                    icon="arrow_back"
                    size="sm"
                    @click="router.visit(route('projects.index'))"
                >
                    Volver a Proyectos
                </SecondaryButton>

                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ isEditing ? 'Editar proyecto' : 'Nuevo proyecto' }}
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ isEditing ? `Modificando: ${project.name}` : 'Completa los datos del nuevo proyecto.' }}
                    </p>
                </div>
            </div>

            <form
                @submit.prevent="submit"
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 divide-y divide-slate-100 dark:divide-slate-700 overflow-hidden"
            >
                <div class="px-6 py-5">
                    <div class="flex items-center gap-2 mb-6 text-slate-500">
                        <span class="material-symbols-outlined block">account_tree</span>
                        <p class="text-xs font-black uppercase tracking-wider">Información del Proyecto</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-1">
                            <InputLabel value="Nombre" :required="true" />
                            <TextInput
                                v-model="form.name"
                                icon="account_tree"
                                placeholder="Ej. Desarrollo web JCB"
                                :error="!!form.errors.name"
                                autofocus
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <div class="md:col-span-1">
                            <InputLabel value="Email de contacto" />
                            <TextInput
                                v-model="form.email"
                                type="email"
                                icon="mail"
                                placeholder="contacto@proyecto.com"
                                :error="!!form.errors.email"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel value="Descripción" />
                            <!-- textarea nativo: no existe TextareaInput como componente compartido en el proyecto -->
                            <textarea
                                v-model="form.description"
                                rows="3"
                                placeholder="Describe brevemente el proyecto..."
                                class="mt-1 w-full px-3.5 py-2.5 text-sm rounded-xl resize-none border bg-white dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 transition-all duration-150"
                                :class="form.errors.description
                                    ? 'border-red-500 focus:ring-red-500/30'
                                    : 'border-slate-200 dark:border-slate-700 focus:ring-brand/30 focus:border-brand'"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                        </div>

                        <div class="md:col-span-1">
                            <InputLabel value="Compañía" />
                            <div class="mt-1 flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 opacity-80">
                                <span class="material-symbols-outlined text-slate-400 text-[18px]">business</span>
                                <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">
                                    {{ company?.name ?? '—' }}
                                </span>
                            </div>
                        </div>

                        <div v-if="can.deactivate" class="md:col-span-1 flex flex-col justify-end pb-1">
                            <InputLabel value="Estado del Proyecto" class="mb-2" />
                            <label class="flex items-center gap-4 cursor-pointer group w-fit">
                                <div class="relative">
                                    <input v-model="form.is_active" type="checkbox" class="sr-only peer" />
                                    <div class="w-11 h-6 rounded-full bg-slate-200 dark:bg-slate-700 peer-checked:bg-brand transition-colors" />
                                    <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5" />
                                </div>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ form.is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 flex items-center justify-between bg-slate-50 dark:bg-slate-800/60 rounded-b-2xl">
                    <SecondaryButton type="button" icon="close" @click="router.visit(route('projects.index'))">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton
                        type="submit"
                        :loading="form.processing"
                        :icon="isEditing ? 'save' : 'add_task'"
                    >
                        {{ isEditing ? 'Guardar cambios' : 'Crear proyecto' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
