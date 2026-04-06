<template>
    <AppLayout>
        <div class="max-w-2xl">
            <div class="mb-6 flex items-center gap-3">
                <RouterLink :to="{ name: 'domains.show', params: { id: domainId } }"
                            class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </RouterLink>
                <h1 class="text-2xl font-bold text-gray-900">Редактировать домен</h1>
            </div>

            <div v-if="fetching" class="card p-6 space-y-4">
                <div v-for="i in 4" :key="i" class="h-10 rounded bg-gray-100 animate-pulse" />
            </div>

            <div v-else class="card p-6">
                <DomainForm
                    :initial="initial"
                    :errors="errors"
                    :loading="loading"
                    submit-label="Сохранить изменения"
                    @submit="handleSubmit"
                    @cancel="router.push({ name: 'domains.show', params: { id: domainId } })"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { domainsApi } from '@/api/domains'
import { useFlashStore } from '@/stores/flash'
import { useFormErrors } from '@/composables/useFormErrors'
import AppLayout from '@/components/AppLayout.vue'
import DomainForm from '@/components/DomainForm.vue'

const route    = useRoute()
const router   = useRouter()
const flash    = useFlashStore()
const domainId = route.params.id

const fetching = ref(true)
const loading  = ref(false)
const initial  = ref({})

const { errors, clearErrors, handleError } = useFormErrors()

onMounted(async () => {
    try {
        const { data } = await domainsApi.get(domainId)
        initial.value = data.data
    } catch {
        flash.error('Домен не найден.')
        router.push({ name: 'domains.index' })
    } finally {
        fetching.value = false
    }
})

async function handleSubmit(form) {
    clearErrors()
    loading.value = true
    try {
        await domainsApi.update(domainId, form)
        flash.success('Домен успешно обновлён.')
        router.push({ name: 'domains.show', params: { id: domainId } })
    } catch (err) {
        handleError(err)
    } finally {
        loading.value = false
    }
}
</script>
