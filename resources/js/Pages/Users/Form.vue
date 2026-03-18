<script setup>
// resources/js/Pages/Users/Form.vue
import { computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/shared/components/InputLabel.vue'
import SelectInput from '@/shared/components/SelectInput.vue'
import TextInput from '@/shared/components/TextInput.vue'
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue'
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue'
import { useAuth } from '@/shared/composables/useAuth'
import { roleOptions } from '@/shared/constants/roles'

const props = defineProps({
    user: { type: Object, default: null },
    companies: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
})

const { isSuperAdmin } = useAuth()
const isEditing = computed(() => props.user !== null)

// Solo muestra los roles que el backend permite para este contexto
const filteredRoleOptions = computed(() =>
    roleOptions.filter(r => props.roles.includes(r.id))
)

const companyOptions = computed(() =>
    props.companies.map(c => ({ id: c.id, name: c.name }))
)

const form = useForm({
    company_id: props.user?.company_id ?? (props.companies[0]?.id ?? null),
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    role: props.user?.role ?? '',
    password: '',
    password_confirmation: '',
    is_active: props.user?.is_active ?? true,
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('users.update', props.user.id), { preserveScroll: true })
    } else {
        form.post(route('users.store'))
    }
}
</script>

<template>

    <Head :title="isEditing ? 'Editar usuario' : 'Nuevo usuario'" />

    <AuthenticatedLayout>
        <div class="max-w-full mx-auto flex flex-col gap-5">

            <div class="flex flex-col gap-2">
                <SecondaryButton
                    type="button"
                    icon="arrow_back"
                    size="sm"
                    @click="router.visit(route('users.index'))"
                >
                    Volver a Usuarios
                </SecondaryButton>

                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ isEditing ? 'Editar usuario' : 'Nuevo usuario' }}
                </h1>
            </div>

            <form @submit.prevent="submit"
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="divide-y divide-slate-100 dark:divide-slate-700">

                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-6 text-slate-500">
                            <span class="material-symbols-outlined block">person_add</span>
                            <p class="text-xs font-black uppercase tracking-wider">Perfil del Usuario</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <InputLabel value="Nombre completo" :required="true" />
                                <TextInput v-model="form.name" icon="person" placeholder="Ej. Juan García"
                                    :error="!!form.errors.name" autofocus />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <InputLabel value="Correo electrónico" :required="true" />
                                <TextInput v-model="form.email" type="email" icon="mail"
                                    placeholder="usuario@empresa.com" :error="!!form.errors.email" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                            </div>

                            <div>
                                <InputLabel value="Rol de acceso" :required="true" />
                                <SelectInput v-model="form.role" :options="filteredRoleOptions" placeholder="Seleccionar..." />
                                <p v-if="form.errors.role" class="mt-1 text-xs text-red-500">{{ form.errors.role }}</p>
                            </div>

                            <div v-if="isSuperAdmin"
                                class="lg:col-span-3 bg-slate-50 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                                <div class="max-w-md">
                                    <InputLabel value="Asignar a Compañía" :required="true" />
                                    <SelectInput v-model="form.company_id" :options="companyOptions"
                                        placeholder="Seleccionar..." />
                                    <p v-if="form.errors.company_id" class="mt-1 text-xs text-red-500">{{
                                        form.errors.company_id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50/50 dark:bg-slate-800/20">
                        <div class="flex items-center gap-2 mb-6 text-slate-500">
                            <span class="material-symbols-outlined block">lock</span>
                            <p class="text-xs font-black uppercase tracking-wider">Credenciales</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                            <div>
                                <InputLabel value="Contraseña" :required="!isEditing" />
                                <TextInput v-model="form.password" type="password" icon="key"
                                    :placeholder="isEditing ? '••••••••' : 'Mínimo 8 caracteres'"
                                    :error="!!form.errors.password" />
                                <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <InputLabel value="Confirmar contraseña" :required="!isEditing" />
                                <TextInput v-model="form.password_confirmation" type="password" icon="lock_reset"
                                    placeholder="Repetir contraseña" />
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <label
                            class="flex items-center gap-4 cursor-pointer p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all w-fit min-w-[320px]">
                            <div class="relative">
                                <input v-model="form.is_active" type="checkbox" class="sr-only peer" />
                                <div
                                    class="w-11 h-6 rounded-full bg-slate-200 dark:bg-slate-700 peer-checked:bg-brand transition-colors" />
                                <div
                                    class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900 dark:text-white leading-tight">
                                    {{ form.is_active ? 'Usuario Activo' : 'Usuario Inactivo' }}
                                </span>
                                <span class="text-[11px] text-slate-500">
                                    Control de acceso al sistema
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="px-6 py-4 flex items-center justify-between bg-slate-50 dark:bg-slate-800/60 rounded-b-2xl">
                        <SecondaryButton type="button" icon="close" @click="router.visit(route('users.index'))">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :loading="form.processing"
                            :icon="isEditing ? 'save' : 'person_add'" size="md">
                            {{ isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}
                        </PrimaryButton>
                    </div>

                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
