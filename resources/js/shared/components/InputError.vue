<script setup>
defineProps({
    message: { type: String, default: '' },
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <p v-if="message" class="flex items-center gap-1 mt-1.5 text-xs text-danger">
            <span class="material-symbols-outlined text-sm">error</span>
            {{ message }}
        </p>
    </Transition>
</template>
