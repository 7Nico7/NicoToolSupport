// resources/js/shared/composables/useAuth.js
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useAuth() {
    const page = usePage()
    const authUser = computed(() => page.props.auth.user)
    const isSuperAdmin = computed(() => authUser.value.role === 'super_admin')

    return { authUser, isSuperAdmin }
}
