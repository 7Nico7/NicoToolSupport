// resources/js/features/Kanban/composables/useTicketForm.js
import { ref, computed } from 'vue';

/**
 * Composable que encapsula la lógica del formulario de tickets.
 * Usado tanto en CreateTicketModal como en EditTicketModal.
 */
export function useTicketForm(initialData = {}) {
    const defaults = {
        title:       '',
        description: '',
        status_id:   null,
        priority_id: null,
        type_id:     null,
        category_id: null,
        project_id:  null,
        helpdesk_id: null,
        assigned_to: null,
        due_date:    '',
    };

    const form   = ref({ ...defaults, ...initialData });
    const errors = ref({});
    const dirty  = ref(false);

    const isValid = computed(() =>
        form.value.title.trim().length > 0 && form.value.status_id !== null
    );

    function reset(data = {}) {
        form.value   = { ...defaults, ...data };
        errors.value = {};
        dirty.value  = false;
    }

    function populate(ticket) {
        form.value = {
            title:       ticket.title       ?? '',
            description: ticket.description ?? '',
            status_id:   ticket.status_id,
            priority_id: ticket.priority_id ?? null,
            type_id:     ticket.type_id     ?? null,
            category_id: ticket.category_id ?? null,
            project_id:  ticket.project_id  ?? null,
            helpdesk_id: ticket.helpdesk_id ?? null,
            assigned_to: ticket.assigned_to ?? null,
            due_date:    ticket.due_date    ?? '',
        };
        errors.value = {};
        dirty.value  = false;
    }

    function setError(field, message) {
        errors.value[field] = message;
    }

    function clearErrors() {
        errors.value = {};
    }

    function handleServerErrors(responseErrors = {}) {
        errors.value = responseErrors;
    }

    function touch() {
        dirty.value = true;
    }

    return {
        form,
        errors,
        dirty,
        isValid,
        reset,
        populate,
        setError,
        clearErrors,
        handleServerErrors,
        touch,
    };
}
