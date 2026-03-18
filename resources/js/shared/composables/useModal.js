import { ref, reactive } from 'vue'

export function useModal() {
    const isOpen = ref(false)
    const item = ref(null)

    const open = (payload = null) => {
        item.value = payload
        isOpen.value = true
    }

    const close = () => {
        console.log("Cerrando modal...");
        isOpen.value = false
        setTimeout(() => { item.value = null }, 300)
    }

    // Al retornar un reactive, el template no se confunde con los .value
    return reactive({
        isOpen,
        item,
        open,
        close
    })
}
