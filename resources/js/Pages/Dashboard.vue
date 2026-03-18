<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/shared/components/Card.vue';
import Table from '@/shared/components/Datatable.vue';
import Badge from '@/shared/components/Badge.vue';
import PrimaryButton from '@/shared/components/buttons/PrimaryButton.vue';
import SecondaryButton from '@/shared/components/buttons/SecondaryButton.vue';


const currentPage = ref(1);

const stats = ref([
    { icon: 'person', label: 'Desarrolladores Disponibles', value: 8, change: '+2% hoy', color: 'blue' },
    { icon: 'person_off', label: 'Desarrolladores Ocupados', value: 14, change: 'Sin cambios', color: 'orange' },
    { icon: 'error', label: 'Tickets Abiertos', value: 32, change: '+5 nuevos', color: 'red' },
    { icon: 'task_alt', label: 'Resueltos este mes', value: 142, change: '94% ratio', color: 'green' },
]);

const tickets = ref([
    { id: '#1024', origin: 'Web', subject: 'Error crítico en pasarela de pago', time: 'hace 12 min', status: 'Abierto', statusColor: 'red', assigned: null },
    { id: '#1025', origin: 'App', subject: 'Crash persistente al iniciar sesión', time: 'Asignado a: Marcos P.', status: 'En Proceso', statusColor: 'orange', assigned: 'Marcos P.' },
    { id: '#1026', origin: 'API', subject: 'Timeout recurrente en endpoint /auth', time: 'Prioridad: Alta', status: 'Abierto', statusColor: 'red', assigned: null },
    { id: '#1027', origin: 'Web', subject: 'Imágenes de productos no cargan', time: 'Asignado a: Carla S.', status: 'En Proceso', statusColor: 'orange', assigned: 'Carla S.' },
    { id: '#1024', origin: 'Web', subject: 'Error crítico en pasarela de pago', time: 'hace 12 min', status: 'Abierto', statusColor: 'red', assigned: null },
    { id: '#1025', origin: 'App', subject: 'Crash persistente al iniciar sesión', time: 'Asignado a: Marcos P.', status: 'En Proceso', statusColor: 'orange', assigned: 'Marcos P.' },
    { id: '#1026', origin: 'API', subject: 'Timeout recurrente en endpoint /auth', time: 'Prioridad: Alta', status: 'Abierto', statusColor: 'red', assigned: null },
    { id: '#1027', origin: 'Web', subject: 'Imágenes de productos no cargan', time: 'Asignado a: Carla S.', status: 'En Proceso', statusColor: 'orange', assigned: 'Carla S.' },


]);

const columns = [
    { key: 'id', label: 'ID' },
    { key: 'origin', label: 'Origen' },
    { key: 'subject', label: 'Asunto' },
    { key: 'status', label: 'Estado' },
    { key: 'actions', label: 'Acciones', class: 'text-right' },
];
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <Card v-for="stat in stats" :key="stat.label" padding="md">
                    <div class="flex items-center justify-between mb-4">
                        <span
                            class="material-symbols-outlined"
                            :class="`text-${stat.color}-600 dark:text-${stat.color}-400`"
                        >
                            {{ stat.icon }}
                        </span>
                        <span
                            class="text-xs font-bold"
                            :class="{
                                'text-green-600 dark:text-green-400': stat.change.includes('+'),
                                'text-red-600 dark:text-red-400': stat.change.includes('-') || stat.change.includes('nuevos'),
                                'text-slate-500 dark:text-slate-400': !stat.change.includes('+') && !stat.change.includes('-') && !stat.change.includes('nuevos')
                            }"
                        >
                            {{ stat.change }}
                        </span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">{{ stat.label }}</p>
                    <p class="text-3xl font-black mt-1 text-slate-900 dark:text-white">{{ stat.value }}</p>
                </Card>
            </div>

            <!-- Tickets Table -->
            <Card padding="none" class="!border-0"> <!-- Elimina el borde del Card -->
                <template #header>
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tickets Recientes</h3>
                        <div class="flex gap-2">
                            <SecondaryButton size="sm">Filtrar</SecondaryButton>
                            <PrimaryButton size="sm">Nuevo Ticket</PrimaryButton>
                        </div>
                    </div>
                </template>

                <Table :columns="columns" :rows="tickets" hoverable>
                    <!-- Columna Origen -->
                    <template #cell-origin="{ value }">
                        <Badge
                            :variant="
                                value === 'Web' ? 'primary' :
                                value === 'App' ? 'warning' : 'info'
                            "
                        >
                            {{ value }}
                        </Badge>
                    </template>

                    <!-- Columna Estado -->
                    <template #cell-status="{ value }">
                        <Badge :variant="value === 'Abierto' ? 'danger' : 'warning'" dot>
                            {{ value }}
                        </Badge>
                    </template>

                    <!-- Columna Acciones -->
                    <template #cell-actions="{ row }">
                        <div class="flex justify-end gap-2">
                            <PrimaryButton
                                v-if="row.status === 'Abierto'"
                                size="sm"
                                icon="person_add"
                            />
                            <SecondaryButton v-else size="sm" icon="visibility" />
                        </div>
                    </template>
                </Table>


            </Card>
        </div>
    </AuthenticatedLayout>
</template>
