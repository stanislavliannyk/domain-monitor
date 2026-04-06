<template>
    <span :class="['inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold', styles.badge]">
        <span :class="['inline-block h-1.5 w-1.5 rounded-full', styles.dot]" />
        {{ styles.label }}
    </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: v => ['up', 'down', 'unknown'].includes(v),
    },
})

const map = {
    up:      { badge: 'bg-green-100 text-green-800', dot: 'bg-green-500', label: 'UP' },
    down:    { badge: 'bg-red-100 text-red-800',     dot: 'bg-red-500',   label: 'DOWN' },
    unknown: { badge: 'bg-gray-100 text-gray-600',   dot: 'bg-gray-400',  label: 'UNKNOWN' },
}

const styles = computed(() => map[props.status] ?? map.unknown)
</script>
