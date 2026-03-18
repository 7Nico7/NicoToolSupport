# Componentes UI — Guía de Uso

Todos los componentes usan el tema Tailwind del proyecto:
`brand` (#2563eb), `primary`, `success`, `warning`, `danger`, `info`, dark mode con `.dark`.

---

## Botones

```vue
<PrimaryButton>Guardar</PrimaryButton>
<PrimaryButton icon="add">Nuevo Ticket</PrimaryButton>
<PrimaryButton :loading="saving" size="sm">Crear</PrimaryButton>

<SecondaryButton icon="close">Cancelar</SecondaryButton>

<DangerButton>Eliminar</DangerButton>
<DangerButton variant="ghost" icon="delete">Eliminar</DangerButton>

<!-- Props compartidas: type, disabled, loading, size (sm|md|lg), icon -->
```

---

## Inputs

```vue
<!-- TextInput -->
<InputLabel value="Correo" required />
<TextInput
    v-model="form.email"
    type="email"
    placeholder="usuario@empresa.com"
    icon="mail"
    :error="!!errors.email"
/>
<InputError :message="errors.email?.[0]" />

<!-- Con ícono al final (trailing) -->
<TextInput
    v-model="search"
    icon="search"
    icon-trail="close"
    @trailing-click="search = ''"
/>
```

---

## Checkbox

```vue
<Checkbox v-model="form.is_active" label="Activo" />

<!-- Sin label, solo el check -->
<Checkbox v-model="selected" />

<!-- Con slot -->
<Checkbox v-model="form.terms">
    Acepto los <a href="#" class="text-brand underline">términos y condiciones</a>
</Checkbox>
```

---

## Dropdown

```vue
<Dropdown align="right" width="48">
    <template #trigger>
        <SecondaryButton icon="more_vert" size="sm" />
    </template>

    <DropdownLink icon="edit" as="button" @click="openEdit">Editar</DropdownLink>
    <DropdownLink icon="content_copy" as="button">Duplicar</DropdownLink>
    <DropdownLink icon="delete" danger as="button" @click="confirmDelete">Eliminar</DropdownLink>
</Dropdown>

<!-- Para Inertia Links -->
<DropdownLink :href="route('profile')" icon="person" as="Link" :active="route().current('profile')">
    Mi perfil
</DropdownLink>
```

---

## NavLink / ResponsiveNavLink

```vue
<!-- Barra de nav principal -->
<NavLink :href="route('kanban')" icon="view_kanban" :active="route().current('kanban')">
    Kanban
</NavLink>

<!-- Menú mobile -->
<ResponsiveNavLink :href="route('kanban')" icon="view_kanban" :active="route().current('kanban')">
    Kanban
</ResponsiveNavLink>
```

---

## Card

```vue
<!-- Básica -->
<Card>Contenido</Card>

<!-- Con header y footer via slots -->
<Card variant="elevated" padding="lg">
    <template #header>
        <h3 class="font-bold text-slate-900 dark:text-white">Título</h3>
    </template>

    Contenido principal aquí

    <template #footer>
        <PrimaryButton>Guardar</PrimaryButton>
    </template>
</Card>

<!-- Con borde de acento (útil para estados/prioridades) -->
<Card accent="#ef4444" accent-side="left" hoverable>
    Ticket crítico
</Card>

<!-- Variantes: default | flat | outlined | elevated -->
<!-- Padding: none | sm | md | lg -->
```

---

## DataTable

```vue
<DataTable
    :columns="[
        { key: 'ticket_number', label: '#',        sortable: true, width: '24' },
        { key: 'title',         label: 'Título',   sortable: true },
        { key: 'status',        label: 'Estado' },
        { key: 'priority',      label: 'Prioridad' },
        { key: 'assigned_user', label: 'Agente' },
        { key: 'created_at',    label: 'Fecha',    sortable: true, align: 'right' },
    ]"
    :rows="tickets"
    :loading="store.loading"
    empty-text="No hay tickets que coincidan con los filtros"
    empty-icon="confirmation_number"
    row-key="id"
    hoverable
    @row-click="openEdit"
    @sort="handleSort"
>
    <!-- Celdas con formato personalizado -->
    <template #cell-status="{ value }">
        <span
            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold text-white"
            :style="{ backgroundColor: value?.color }"
        >{{ value?.name }}</span>
    </template>

    <template #cell-priority="{ value }">
        <span class="text-xs font-bold" :style="{ color: value?.color }">
            {{ value?.name ?? '—' }}
        </span>
    </template>

    <!-- Columna de acciones -->
    <template #actions="{ row }">
        <Dropdown align="right" width="40">
            <template #trigger>
                <button class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-symbols-outlined text-base text-slate-400">more_vert</span>
                </button>
            </template>
            <DropdownLink icon="edit" as="button" @click.stop="openEdit(row.id)">Editar</DropdownLink>
            <DropdownLink icon="delete" danger as="button" @click.stop="confirmDelete(row.id)">Eliminar</DropdownLink>
        </Dropdown>
    </template>
</DataTable>
```

---

## FilterPanel

```vue
<!-- Modo inline (barra horizontal) -->
<FilterPanel layout="inline" :active-count="activeFilterCount" @clear="clearFilters">
    <select v-model="filters.status_id" class="form-input text-sm w-40">
        <option value="">Todos los estados</option>
        <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.name }}</option>
    </select>

    <select v-model="filters.priority_id" class="form-input text-sm w-40">
        <option value="">Todas las prioridades</option>
        <option v-for="p in priorities" :key="p.id" :value="p.id">{{ p.name }}</option>
    </select>
</FilterPanel>

<!-- Modo panel colapsable (sidebar o bloque en la página) -->
<FilterPanel
    layout="panel"
    title="Filtrar tickets"
    :active-count="activeFilterCount"
    @clear="clearFilters"
>
    <div>
        <InputLabel value="Estado" />
        <select v-model="filters.status_id" class="form-input text-sm">
            <option value="">Todos</option>
            <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
    </div>

    <div>
        <InputLabel value="Prioridad" />
        <select v-model="filters.priority_id" class="form-input text-sm">
            <option value="">Todas</option>
            <option v-for="p in priorities" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
    </div>

    <div>
        <InputLabel value="Agente asignado" />
        <select v-model="filters.assigned_to" class="form-input text-sm">
            <option value="">Todos</option>
            <option v-for="a in agents" :key="a.id" :value="a.id">{{ a.name }}</option>
        </select>
    </div>

    <template #footer>
        <PrimaryButton size="sm" @click="applyFilters">Aplicar</PrimaryButton>
    </template>
</FilterPanel>
```

---

## Clases CSS utilitarias (en kanban.css / app.css)

```css
/* Disponibles via @layer components */
.form-input   /* input/select/textarea estilizado */
.form-label   /* label en mayúsculas pequeñas */
.form-error   /* mensaje de error rojo */
.btn-primary  /* equivale a <PrimaryButton> pero como clase */
.btn-secondary
.btn-danger
```
