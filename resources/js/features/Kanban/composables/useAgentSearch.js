// resources/js/features/Kanban/composables/useAgentSearch.js
import { ref } from 'vue';
import axios from 'axios';

export function useAgentSearch() {
    const results     = ref([]);
    const isSearching = ref(false);
    let timer         = null;

    // companyId: pasado por super_admin para acotar la búsqueda a una compañía específica.
    // Para otros roles es null y el backend usa $user->company_id automáticamente.
    async function search(query, companyId = null) {
        clearTimeout(timer);

        if (!query?.trim()) {
            results.value = [];
            return;
        }

        isSearching.value = true;

        timer = setTimeout(async () => {
            try {
                const params = { q: query };
                if (companyId) params.company_id = companyId;

                const { data } = await axios.get('/api/kanban/agents/search', { params });
                results.value = data;
            } catch {
                results.value = [];
            } finally {
                isSearching.value = false;
            }
        }, 300);
    }

    function clear() {
        results.value     = [];
        isSearching.value = false;
        clearTimeout(timer);
    }

    return { results, isSearching, search, clear };
}
