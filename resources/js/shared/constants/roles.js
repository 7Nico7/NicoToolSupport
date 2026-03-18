// resources/js/shared/constants/roles.js

export const ROLE_CONFIG = {
    super_admin: {
        label: 'Super Admin',
        classes: 'bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400',
    },
    admin: {
        label: 'Admin',
        classes: 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    },
    agent: {
        label: 'Agente',
        classes: 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
    },
    client: {
        label: 'Cliente',
        classes: 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
    },
}

// Lista de roles como opciones para SelectInput
export const roleOptions = Object.entries(ROLE_CONFIG).map(([id, { label }]) => ({ id, name: label }))
