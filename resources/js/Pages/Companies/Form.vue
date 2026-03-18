<script setup>
// resources/js/Pages/Companies/Form.vue
import { computed, ref } from 'vue'
import { Head, useForm, usePage, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Componentes UI
import InputLabel from '@/shared/components/InputLabel.vue'
import TextInput from '@/shared/components/TextInput.vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'

const props = defineProps({
    company: { type: Object, default: null },
    can: { type: Object, default: () => ({}) },
})

const page = usePage()
const isSuperAdmin = computed(() => page.props.auth.user.role === 'super_admin')
const isEditing = computed(() => props.company !== null)

const form = useForm({
    name: props.company?.name ?? '',
    is_active: props.company?.is_active ?? true,
    logo: null,
})

const logoPreview = ref(props.company?.logo ? `/storage/${props.company.logo}` : null)
const logoInput = ref(null)

const onLogoChange = (e) => {
    const file = e.target.files[0]
    if (!file) return
    form.logo = file
    logoPreview.value = URL.createObjectURL(file)
}

const removeLogo = () => {
    form.logo = null
    logoPreview.value = null
    if (logoInput.value) logoInput.value.value = ''
}

const submit = () => {
    const options = { preserveScroll: true, forceFormData: true }
    if (isEditing.value) {
        form.transform(data => ({ ...data, _method: 'PUT' }))
            .post(route('companies.update', props.company.id), options)
    } else {
        form.post(route('companies.store'), options)
    }
}

const generatedSlug = computed(() => {
    return form.name
        ? form.name.toLowerCase().trim()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '')
        : 'slug-automatico'
})
</script>

<template>

    <Head :title="isEditing ? 'Editar compañía' : 'Nueva compañía'" />

    <AuthenticatedLayout>
        <div class="w-full flex flex-col gap-6">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <Link :href="route('companies.index')"
                        class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand transition-colors font-bold mb-2">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Volver al listado
                    </Link>
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ isEditing ? 'Configuración de Compañía' : 'Registro de Nueva Compañía' }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <SecondaryButton @click="router.visit(route('companies.index'))">
                        Descartar
                    </SecondaryButton>
                    <PrimaryButton @click="submit" :loading="form.processing"
                        :icon="isEditing ? 'save' : 'add_business'">
                        {{ isEditing ? 'Actualizar' : 'Registrar Empresa' }}
                    </PrimaryButton>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-6">Logo
                            Corporativo</h3>

                        <div class="flex flex-col items-center gap-6">
                            <div
                                class="h-40 w-40 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center overflow-hidden relative group">
                                <img v-if="logoPreview" :src="logoPreview" class="h-full w-full object-contain p-4" />
                                <span v-else class="material-symbols-outlined text-5xl text-slate-300">image</span>
                            </div>

                            <div class="w-full flex flex-col gap-2">
                                <input ref="logoInput" type="file" accept="image/*" class="hidden"
                                    @change="onLogoChange" />
                                <SecondaryButton class="w-full justify-center" @click="logoInput.click()">
                                    <span class="material-symbols-outlined text-[18px] mr-2">upload</span>
                                    Subir Imagen
                                </SecondaryButton>
                                <button v-if="logoPreview" type="button" @click="removeLogo"
                                    class="text-xs font-bold text-red-500 hover:text-red-600 transition-colors py-2">
                                    Eliminar imagen actual
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="isSuperAdmin"
                        class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 transition-colors">

                        <h3
                            class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">
                            Estatus Operativo
                        </h3>

                        <label class="flex items-center justify-between cursor-pointer group">
                            <span
                                class="text-sm font-bold text-slate-700 dark:text-white group-hover:text-brand transition-colors">
                                Permitir acceso al sistema
                            </span>

                            <div class="relative">
                                <input v-model="form.is_active" type="checkbox" class="sr-only peer" />

                                <div class="w-11 h-6 rounded-full transition-all shadow-inner
                        bg-slate-200 dark:bg-slate-700
                        peer-checked:bg-brand">
                                </div>

                                <div class="absolute top-[2px] left-[2px] w-5 h-5 rounded-full bg-white shadow-md transition-transform
                        peer-checked:translate-x-full">
                                </div>
                            </div>
                        </label>
                    </div>

                </div>

                <div
                    class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700/50 flex items-center gap-3">
                        <span class="material-symbols-outlined text-brand">info</span>
                        <h3 class="font-bold text-slate-900 dark:text-white">Información General</h3>
                    </div>

                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <InputLabel value="Razón Social / Nombre Comercial" :required="true" />
                                <TextInput v-model="form.name" icon="business" placeholder="Nombre de la empresa"
                                    :error="!!form.errors.name" />
                                <p v-if="form.errors.name" class="text-xs font-bold text-red-500 mt-1">{{
                                    form.errors.name }}</p>
                            </div>

                            <div class="space-y-2">
                                <InputLabel value="Identificador de Sistema (Slug)" />
                                <div
                                    class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30">
                                    <span class="material-symbols-outlined text-slate-400 text-[18px]">link</span>
                                    <span class="font-mono text-sm text-slate-500">{{ generatedSlug }}</span>
                                </div>
                                <p class="text-[10px] text-slate-400 italic">Este campo se utiliza para la estructura de
                                    las URLs del ERP.</p>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-50 dark:border-slate-700/50">
                            <div
                                class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-2xl p-4 flex gap-4">
                                <span class="material-symbols-outlined text-blue-500">lightbulb</span>
                                <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                    Al registrar una nueva compañía, se creará un entorno aislado (Tenant). Asegúrate de
                                    que el nombre coincida exactamente con los registros fiscales para evitar
                                    confusiones en los módulos de facturación y logística.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
