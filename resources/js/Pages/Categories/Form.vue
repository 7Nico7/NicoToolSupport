<script setup>
// resources/js/Pages/Categories/Form.vue
import { computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel      from '@/shared/components/InputLabel.vue'
import TextInput       from '@/shared/components/TextInput.vue'
import SelectInput     from '@/shared/components/SelectInput.vue'
import PrimaryButton   from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'
import { useAuth }     from '@/shared/composables/useAuth'
import PageHeader from '@/shared/components/PageHeader.vue'
const props = defineProps({
    category:  { type: Object, default: null },
    company:   { type: Object, default: null },  // compañía del usuario o del registro
    companies: { type: Array,  default: () => [] },
    can:       { type: Object, default: () => ({}) },
})

const { isSuperAdmin } = useAuth()
const isEditing = computed(() => props.category !== null)

const pageTitle = computed(() => isEditing.value ? 'Editar categoría' : 'Nuevo categoría')

const companyOptions = computed(() =>
    props.companies.map(c => ({ id: c.id, name: c.name }))
)

const form = useForm({
    name:       props.category?.name       ?? '',
    company_id: props.category?.company_id ?? props.companies[0]?.id ?? null,
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('categories.update', props.category.id), { preserveScroll: true })
    } else {
        form.post(route('categories.store'))
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar categoría' : 'Nueva categoría'" />

    <AuthenticatedLayout>
        <div class="max-w-full mx-auto flex flex-col gap-5">

            <!-- ── Header ─────────────────────────────────────────────────── -->
                        <PageHeader
                :title="pageTitle"
                :description="isEditing
                    ? 'Modifica la categoría.'
                    : 'Crea una nueva categoría.'"
                back-label="Volver a Categorias"
                :back-href="route('categories.index')"
            />


            <!-- ── Formulario ─────────────────────────────────────────────── -->
            <form @submit.prevent="submit"
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="divide-y divide-slate-100 dark:divide-slate-700">

                    <!-- Datos principales -->
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-6 text-slate-500">
                            <span class="material-symbols-outlined">category</span>
                            <p class="text-xs font-black uppercase tracking-wider">Datos de la categoría</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">

                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <InputLabel value="Nombre" :required="true" />
                                <TextInput
                                    v-model="form.name"
                                    icon="label"
                                    placeholder="Ej. Soporte técnico, Facturación..."
                                    :error="!!form.errors.name"
                                    autofocus
                                />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <!-- Compañía — selector para super_admin, readonly para el resto -->
                            <div class="md:col-span-2">
                                <InputLabel value="Compañía" :required="isSuperAdmin" />

                                <SelectInput
                                    v-if="isSuperAdmin"
                                    v-model="form.company_id"
                                    :options="companyOptions"
                                    class="mt-1"
                                />
                                <div v-else
                                    class="mt-1 flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl
                                           border border-slate-200 dark:border-slate-700
                                           bg-slate-50 dark:bg-slate-900/40 opacity-80">
                                    <span class="material-symbols-outlined text-slate-400 text-[18px]">business</span>
                                    <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">
                                        {{ company?.name ?? '—' }}
                                    </span>
                                </div>

                                <p v-if="form.errors.company_id" class="mt-1 text-xs text-red-500">
                                    {{ form.errors.company_id }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Footer acciones -->
                    <div class="px-6 py-4 flex items-center justify-between bg-slate-50 dark:bg-slate-800/60">
                        <SecondaryButton type="button" icon="close"
                            @click="router.visit(route('categories.index'))">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :loading="form.processing"
                            :icon="isEditing ? 'save' : 'add_task'">
                            {{ isEditing ? 'Guardar cambios' : 'Crear categoría' }}
                        </PrimaryButton>
                    </div>

                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
