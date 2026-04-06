<template>
    <div>
        <label v-if="label" :for="id" class="form-label">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-0.5">*</span>
        </label>
        <slot />
        <p v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</p>
        <p v-else-if="hint" class="mt-1 text-xs text-gray-400">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    id:       String,
    label:    String,
    error:    String,
    hint:     String,
    required: Boolean,
})
</script>
