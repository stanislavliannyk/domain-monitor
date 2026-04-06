<template>
    <div v-if="meta.last_page > 1" class="flex items-center justify-between px-2">
        <p class="text-sm text-gray-500">
            Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total }}
        </p>
        <div class="flex gap-1">
            <button
                v-for="page in visiblePages"
                :key="page"
                :disabled="page === meta.current_page"
                @click="$emit('change', page)"
                :class="[
                    'px-3 py-1 rounded text-sm transition-colors',
                    page === meta.current_page
                        ? 'bg-blue-600 text-white font-semibold'
                        : 'text-gray-600 hover:bg-gray-100',
                ]"
            >{{ page }}</button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    meta: {
        type: Object,
        required: true,
    },
})

defineEmits(['change'])

const visiblePages = computed(() => {
    const { current_page, last_page } = props.meta
    const delta = 2
    const range = []
    for (let i = Math.max(1, current_page - delta); i <= Math.min(last_page, current_page + delta); i++) {
        range.push(i)
    }
    return range
})
</script>
