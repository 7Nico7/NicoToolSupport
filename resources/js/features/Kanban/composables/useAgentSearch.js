// resources/js/features/Kanban/composables/useAgentSearch.js
import { ref } from 'vue';
import axios from 'axios';

/**
 * Composable para búsqueda debounced de agentes.
 * Encapsula la lógica que antes estaba duplicada en UserSearchInput.
 */
export function useAgentSearch() {
    const results     = ref([]);
    const isSearching = ref(false);
    let timer         = null;

    async function search(query) {
        clearTimeout(timer);

        if (!query?.trim()) {
            results.value = [];
            return;
        }

        isSearching.value = true;

        timer = setTimeout(async () => {
            try {
                const { data } = await axios.get('/api/kanban/agents/search', {
                    params: { q: query },
                });
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
